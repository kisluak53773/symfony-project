<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Services\OrderService;
use App\Services\Exception\Request\RequestException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\DTO\PaginationQueryDto;

#[Route('/api/order', name: 'api_order_')]
class OrderController extends AbstractController
{
    public function __construct(private OrderService $orderService) {}

    #[Route(name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function createOrderDto(#[MapRequestPayload] CreateOrderDto $createOrderDto): JsonResponse
    {
        try {
            $this->orderService->createOrderDto($createOrderDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'order created'], 200);
    }

    #[Route('/current', name: 'get_orders_of_current_user', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getUserOrders(#[MapQueryString] PaginationQueryDto $paginationQueryDto): JsonResponse
    {
        try {
            $response = $this->orderService->getUserOrders($paginationQueryDto);
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
    public function getVendorOrders(#[MapQueryString] PaginationQueryDto $paginationQueryDto): JsonResponse
    {
        try {
            $response = $this->orderService->getVendorOrders($paginationQueryDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['orders']]
        );
    }

    #[Route('/vendor/{id}', name: 'get_vendor_order_by_id', methods: 'get', requirements: ['id' => '\d+'])]
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
    public function getAllOrders(#[MapQueryString] PaginationQueryDto $paginationQueryDto): JsonResponse
    {
        try {
            $response = $this->orderService->getAllOrders($paginationQueryDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['orders', 'orders_admin']]
        );
    }

    #[Route('/{id}', name: 'patch_order', methods: 'patch', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function patchOrder(int $id, #[MapRequestPayload] PatchOrderDto $patchOrderDto): JsonResponse
    {
        try {
            $this->orderService->patchOrder($id, $patchOrderDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Succesfully patched'], 200);
    }

    #[Route('/customer/{id}', name: 'cancel_order', methods: 'patch', requirements: ['id' => '\d+'])]
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
