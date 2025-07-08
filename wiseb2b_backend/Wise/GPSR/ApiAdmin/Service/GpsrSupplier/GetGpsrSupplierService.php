<?php

namespace Wise\GPSR\ApiAdmin\Service\GpsrSupplier;

use Wise\Core\ApiAdmin\Helper\AdminApiShareMethodsHelper;
use Wise\Core\ApiAdmin\Service\AbstractGetListAdminApiService;
use Wise\GPSR\ApiAdmin\Service\GpsrSupplier\Interfaces\GetGpsrSupplierServiceInterface;
use Wise\GPSR\Service\GpsrSupplier\Interfaces\ListGpsrSupplierServiceInterface;

class GetGpsrSupplierService extends AbstractGetListAdminApiService implements GetGpsrSupplierServiceInterface
{
    private const HIGH_QUALITY_THRESHOLD = 35;
    private const MEDIUM_QUALITY_THRESHOLD = 20;

    public function __construct(
        AdminApiShareMethodsHelper $adminApiShareMethodsHelper,
        private readonly ListGpsrSupplierServiceInterface $listSupplierService,
    ){
        parent::__construct($adminApiShareMethodsHelper, $listSupplierService);
    }

    /**
     * Defines the mapping for Response DTO fields whose names DO NOT MATCH the domain and require mapping.
     *
     * @param array $fieldMapping
     * @return array
     */
    protected function prepareCustomFieldMapping(array $fieldMapping = []): array
    {
        $fieldMapping = parent::prepareCustomFieldMapping($fieldMapping);

        return array_merge($fieldMapping, [
            'address' => 'address',
        ]);
    }

    /**
     * This method allows transforming individual serviceDto objects before transformation to responseDto.
     * This is where the quality assessment logic will be injected.
     *
     * @param array|null $elementData The data array for a single supplier element (passed by reference).
     * @return void
     */
    protected function prepareElementServiceDtoBeforeTransform(?array &$elementData): void
    {
        parent::prepareElementServiceDtoBeforeTransform($elementData);

        unset($this->fields['address']);
        $this->fields = array_merge($this->fields, [
            'address.name' => 'address.name',
            'address.street' => 'address.street',
            'address.houseNumber' => 'address.houseNumber',
            'address.apartmentNumber' => 'address.apartmentNumber',
            'address.city' => 'address.city',
            'address.postalCode' => 'address.postalCode',
            'address.state' => 'address.state',
            'address.countryCode' => 'address.countryCode',
        ]);

        if (is_array($elementData)) {
            $qualityResult = $this->calculateSupplierQuality($elementData);
            $elementData['qualityScore'] = $qualityResult['score'];
            $elementData['qualityLabel'] = $qualityResult['label'];
        }
    }

    /**
     * Calculates the quality score and label for a given supplier based on defined rules.
     * This method is designed to be easily extensible by adding new scoring rules.
     *
     * @param array $supplierData The supplier data array to evaluate (from $elementData).
     * @return array An associative array containing 'score' (int) and 'label' (string).
     */
    private function calculateSupplierQuality(array $supplierData): array
    {
        $score = 0;

        // Rule: Tax Number - If valid, add 10 points.
        if ($this->isValidTaxNumber($supplierData['taxNumber'] ?? null)) {
            $score += 10;
        }

        // Rule: Email - If valid, add 10 points.
        if ($this->isValidEmail($supplierData['email'] ?? null)) {
            $score += 10;
        }

        // Rule: registeredTradeName - If provided, add 5 points.
        if (!empty($supplierData['registeredTradeName'] ?? null)) {
            $score += 5;
        }

        // Rule: Address - If provided and contains all required fields (street, zip, city, country), add 10 points.
        if (
            !empty($supplierData['addressStreet'] ?? null) &&
            !empty($supplierData['addressZipCode'] ?? null) &&
            !empty($supplierData['addressCity'] ?? null) &&
            !empty($supplierData['addressCountry'] ?? null)
        ) {
            $score += 10;
        }

        // Rule: Phone - If provided and valid, add 5 points.
        if ($this->isValidPhone($supplierData['phone'] ?? null)) {
            $score += 5;
        }

        // Rule: Address Location - If city is "Warszawa", add 5 points.
        if (
            !empty($supplierData['addressCity'] ?? null) &&
            strtolower($supplierData['addressCity'] ?? '') === 'warszawa'
        ) {
            $score += 5;
        }

        $label = $this->getQualityLabel($score);

        return [
            'score' => $score,
            'label' => $label,
        ];
    }

    /**
     * Determines the quality label based on the calculated score.
     *
     * @param int $score The calculated quality score.
     * @return string The quality label (High Quality, Medium Quality, Low Quality).
     */
    private function getQualityLabel(int $score): string
    {
        if ($score >= self::HIGH_QUALITY_THRESHOLD) {
            return 'High Quality';
        } elseif ($score >= self::MEDIUM_QUALITY_THRESHOLD) {
            return 'Medium Quality';
        } else {
            return 'Low Quality';
        }
    }

    /**
     * Validates a tax number (simplified to 10 digits).
     * @param string|null $taxNumber
     * @return bool
     */
    private function isValidTaxNumber(?string $taxNumber): bool
    {
        if (empty($taxNumber)) {
            return false;
        }
        return (bool) preg_match('/^\d{10}$/', preg_replace('/\D/', '', $taxNumber));
    }

    /**
     * Validates an email address.
     * @param string|null $email
     * @return bool
     */
    private function isValidEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validates a phone number (simplified to min 9 digits, max 20 digits, optional leading +).
     * @param string|null $phone
     * @return bool
     */
    private function isValidPhone(?string $phone): bool
    {
        if (empty($phone)) {
            return false;
        }
        $cleanedPhone = preg_replace('/[^\d+]/', '', $phone);
        return (bool) preg_match('/^\+?\d{9,}$/', $cleanedPhone) && strlen($cleanedPhone) <= 20;
    }
}
