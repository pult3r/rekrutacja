<?php

namespace Wise\GPSR\ApiAdmin\Controller\GpsrSupplier;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Wise\Core\Api\Helper\Interfaces\ControllerShareMethodsHelperInterface;
use Wise\Core\ApiAdmin\Attributes\OpenApi\EndpointType\OAGet;
use Wise\Core\ApiAdmin\Controller\AbstractGetListAdminApiController;
use Wise\Core\Security\Constants\ScopeNames;
use Wise\GPSR\ApiAdmin\Service\GpsrSupplier\Interfaces\GetGpsrSupplierServiceInterface;
use Wise\GPSR\ApiAdmin\Dto\GpsrSupplier\GetGpsrSuppliersDto; 

class GetGpsrSupplierController extends AbstractGetListAdminApiController
{
    protected array $requiredApiScopes = [
        ScopeNames::GENERAL_ACCESS,
        ScopeNames::GENERAL_PUT,
        ScopeNames::ORDERS_ALL,
        ScopeNames::ORDERS_PUT,
    ];

    public function __construct(
        private readonly ControllerShareMethodsHelperInterface $endpointShareMethodsHelper,
        private readonly GetGpsrSupplierServiceInterface $service
    ) {
        parent::__construct($endpointShareMethodsHelper, $service);
    }

    #[Route(
        path: '',
        methods: ['GET']
    )]
    #[OAGet(
        description: 'Returns a list of suppliers', 
        tags: ['Supplier'],
        responseDtoClass: GetGpsrSuppliersDto::class,
        isArray: true,
        
        parameters: [
            new OA\Parameter(
                name: 'symbol',
                in: 'query', 
                description: 'Filter by supplier symbol', 
                required: false, 
                schema: new OA\Schema(type: 'string', example: 'WiseB2B') 
            )
        ]
    )]
    public function getAction(Request $request): JsonResponse
    {
        return parent::getAction($request);
    }
}
