<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Services\ProductService;
use App\Services\Exception\Request\RequestException;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/product', name: 'api_product_')]
class ProductController extends AbstractController
{
    public function __construct(private ProductService $productService)
    {
    }

    #[Route('/create', name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function addWithVendor(Request $request): JsonResponse
    {
        try {
            $id = $this->productService->addWithVendor($request);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'new produt craeted', 'id' => $id], 201);
    }

    #[Route(name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        try {
            $response = $this->productService->list($request);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['product_list']]
        );
    }

    #[Route('/vendor', name: 'get_products_vendor_does_not_sell', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getProductsVendorDoesNotSell(Request $request): JsonResponse
    {
        try {
            $response = $this->productService->getProductsVendorDoesNotSell($request);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['vendor_does_not_sell']]
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted(Role::ROLE_ADMIN->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
