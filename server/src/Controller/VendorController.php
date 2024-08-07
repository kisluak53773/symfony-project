<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Services\VendorService;
use App\Services\Exception\Request\RequestException;

#[Route('/api/vendor', name: 'api_vendor_')]
class VendorController extends AbstractController
{
    public function __construct(private VendorService $vendorService)
    {
    }

    #[Route(name: 'add', methods: 'post')]
    public function add(): JsonResponse
    {
        try {
            $id = $this->vendorService->add();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'vendor created', 'id' => $id], 201);
    }

    #[Route('/current', name: 'get_current_vendor', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getCurrentVendor(): JsonResponse
    {
        try {
            $vendor = $this->vendorService->getCurrentVendor();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $vendor,
            context: [AbstractNormalizer::GROUPS => ['current_vendor']]
        );
    }

    #[Route('/current', name: 'patch_current_vendor', methods: 'patch')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function patchCurrentVendor(): JsonResponse
    {
        try {
            $this->vendorService->patchCurrentVendor();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'vendor updated'], 200);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->vendorService->delete($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
