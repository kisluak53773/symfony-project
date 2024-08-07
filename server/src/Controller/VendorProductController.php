<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Enum\Role;
use App\Services\VendorProductService;
use App\Services\Exception\Request\RequestException;

#[Route('/api/vendorProduct', name: 'api_vendorProduct_')]
class VendorProductController extends AbstractController
{
    public function __construct(private VendorProductService $vendorProductService)
    {
    }

    #[Route(name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function add(): JsonResponse
    {
        try {
            $id = $this->vendorProductService->add();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }


        return $this->json(['message' => 'vendor now sells this product', 'id' => $id], 201);
    }

    #[Route('/vendor', name: 'get_for_vendor', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function get(): JsonResponse
    {
        try {
            $response = $this->vendorProductService->get();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['vendor_products']]
        );
    }

    #[Route('/vendor/update/{id<\d+>}', name: 'update_for_vendor', methods: 'patch')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function patchVendorProdut(int $id): JsonResponse
    {
        try {
            $this->vendorProductService->patchVendorProdut($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Updated successfully'], 200);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->vendorProductService->delete($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
