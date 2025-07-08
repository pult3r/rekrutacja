<?php

namespace Wise\GPSR\ApiAdmin\Dto\GpsrSupplier;

use OpenApi\Attributes as OA;

#[OA\Schema(
    description: "GPSR Supplier data model with data quality assessment",
    title: "GetGpsrSuppliersDto"
)]
class GetGpsrSuppliersDto
{
    #[OA\Property(description: "Unique supplier identifier", type: "string", example: "supplier-123")]
    public string $id;

    #[OA\Property(description: "Supplier symbol", type: "string", example: "WiseB2B")]
    public string $symbol;

    #[OA\Property(description: "Supplier Tax/VAT Number", type: "string", example: "PL1234567890")]
    public ?string $taxNumber = null;

    #[OA\Property(description: "Registered trade name", type: "string", example: "Acme Sp. z o.o.")]
    public ?string $registeredTradeName = null;

    #[OA\Property(description: "Contact phone number", type: "string", example: "+48123456789")]
    public ?string $phone = null;

    #[OA\Property(description: "Email address", type: "string", format: "email", example: "contact@example.com")]
    public ?string $email = null;

    #[OA\Property(description: "Street", type: "string", example: "Przykładowa 1")]
    public ?string $addressStreet = null;
    #[OA\Property(description: "Postal code", type: "string", example: "00-001")]
    public ?string $addressZipCode = null;
    #[OA\Property(description: "City", type: "string", example: "Warszawa")]
    public ?string $addressCity = null;
    #[OA\Property(description: "Country", type: "string", example: "Polska")]
    public ?string $addressCountry = null;

    #[OA\Property(
        description: "Supplier data quality score (0-50 points)",
        type: "integer",
        example: 40
    )]
    public int $qualityScore; // New field for the numerical score

    #[OA\Property(
        description: "Supplier data quality label (High Quality, Medium Quality, Low Quality)",
        type: "string",
        enum: ["High Quality", "Medium Quality", "Low Quality"],
        example: "High Quality"
    )]
    public string $qualityLabel; // Renamed from 'quality' to 'qualityLabel' for clarity

    public function __construct(
        string $id,
        string $symbol,
        ?string $taxNumber,
        ?string $registeredTradeName,
        ?string $phone,
        ?string $email,
        ?string $addressStreet,
        ?string $addressZipCode,
        ?string $addressCity,
        ?string $addressCountry
    ) {
        $this->id = $id;
        $this->symbol = $symbol;
        $this->taxNumber = $taxNumber;
        $this->registeredTradeName = $registeredTradeName;
        $this->phone = $phone;
        $this->email = $email;
        $this->addressStreet = $addressStreet;
        $this->addressZipCode = $addressZipCode;
        $this->addressCity = $addressCity;
        $this->addressCountry = $addressCountry;
        
        // Quality fields will be set by the service
        $this->qualityScore = 0; 
        $this->qualityLabel = '';
    }

    public function setQualityScore(int $score): void
    {
        $this->qualityScore = $score;
    }

    public function setQualityLabel(string $label): void
    {
        $this->qualityLabel = $label;
    }
}
