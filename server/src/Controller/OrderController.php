<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\DTO\PaginationQueryDto;
use App\Contract\Service\OrderServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

#[Route('/api/order', name: 'api_order_')]
class OrderController extends AbstractController
{
    public function __construct(private OrderServiceInterface $orderService) {}

    #[Route(name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function createOrder(#[MapRequestPayload] CreateOrderDto $createOrderDto): JsonResponse
    {
        try {
            $this->orderService->createOrder($createOrderDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'order created'], Response::HTTP_CREATED);
    }

    #[Route('/current', name: 'get_orders_of_current_user', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getUserOrders(
        #[MapQueryString] PaginationQueryDto $paginationQueryDto = new PaginationQueryDto()
    ): JsonResponse {
        try {
            $response = $this->orderService->getUserOrders($paginationQueryDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
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
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
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
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
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
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
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
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Succesfully patched'], Response::HTTP_OK);
    }

    #[Route('/customer/{id}', name: 'cancel_order', methods: 'patch', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function cancelOrder(int $id): JsonResponse
    {
        try {
            $this->orderService->cancelOrder($id);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Succesfully patched'], Response::HTTP_OK);
    }
}
