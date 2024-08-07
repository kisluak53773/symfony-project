<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Services\OrderService;
use App\Services\Exception\Request\RequestException;

#[Route('/api/order', name: 'api_order_')]
class OrderController extends AbstractController
{
    public function __construct(private OrderService $orderService)
    {
    }

    #[Route(name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function index(): JsonResponse
    {
        try {
            $this->orderService->index();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'order created'], 200);
    }

    #[Route('/current', name: 'get_orders_of_current_user', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getUserOrders(): JsonResponse
    {
        try {
            $response = $this->orderService->getUserOrders();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['orders']]
        );
    }

    #[Route('/vendor', name: 'get_vendor_orders', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getVendorOrders(): JsonResponse
    {
        try {
            $response = $this->orderService->getVendorOrders();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['orders']]
        );
    }

    #[Route('/vendor/{id<\d+>}', name: 'get_vendor_order_by_id', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getVendorOrderById(int $id): JsonResponse
    {
        try {
            $response = $this->orderService->getVendorOrderById($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['orders', 'vendor_order']]
        );
    }

    #[Route(name: 'get_all_orders', methods: 'get')]
    #[IsGranted(Role::ROLE_ADMIN->value, message: 'You are not allowed to access this route.')]
    public function getAllOrders(): JsonResponse
    {
        try {
            $response = $this->orderService->getAllOrders();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['orders', 'orders_admin']]
        );
    }

    #[Route('/{id<\d+>}', name: 'patch_order', methods: 'patch')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function patchOrder(int $id): JsonResponse
    {
        try {
            $this->orderService->patchOrder($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Succesfully patched'], 200);
    }

    #[Route('/customer/{id<\d+>}', name: 'cancel_order', methods: 'patch')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function cancelOrder(int $id): JsonResponse
    {
        try {
            $this->orderService->cancelOrder($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Succesfully patched'], 200);
    }
}
