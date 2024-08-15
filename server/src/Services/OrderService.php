<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\OrderRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\OrderProductRepositoryInteface;
use App\Contract\Service\OrderServiceInterface;
use App\Contract\PaginationHandlerInterface;
use App\Entity\Order;
use App\Services\Exception\WrongData\CartIsEmptyException;
use App\Services\Exception\NotFound\OrderNotFoundException;
use App\Services\Exception\Access\CanNotCancelOrderException;
use App\Entity\OrderProduct;
use App\Services\Exception\NotFound\CartNotFoundException;
use App\Services\Exception\NotFound\VendorNotFoundException;

class OrderService implements OrderServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Contract\PaginationHandlerInterface<Order> $paginationHandler
     * @param \App\Repository\OrderRepository $orderRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\OrderProductRepository $orderProductRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginationHandlerInterface $paginationHandler,
        private OrderRepositoryInterface $orderRepository,
        private UserRepositoryInterface $userRepository,
        private OrderProductRepositoryInteface $orderProductRepository,
    ) {}

    /**
     * Summary of createOrder
     * @param \App\DTO\Order\CreateOrderDto $createOrderDto
     * 
     * @throws \App\Services\Exception\WrongData\CartIsEmptyException
     * 
     * @return void
     */
    public function createOrder(CreateOrderDto $createOrderDto): void
    {
        $user = $this->userRepository->getCurrentUser();
        $cart = $user->getCart();

        if (!$cart) {
            throw new CartNotFoundException();
        }

        $order = $this->orderRepository->create($createOrderDto, $user);
        $cartProducts = $cart->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            throw new CartIsEmptyException();
        }

        $this->orderProductRepository->addManyProducts($cartProducts, $order);
        $this->entityManager->flush();
    }

    /**
     * Summary of getUserOrders
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function getUserOrders(PaginationQueryDto $paginationQueryDto): array
    {
        $user = $this->userRepository->getCurrentUser();
        $querryBuilder = $this->orderRepository->getAllOrdersBelonignToUser($user);
        $response = $this->paginationHandler->handlePagination($querryBuilder, $paginationQueryDto);

        return $response;
    }

    /**
     * Summary of getVendorOrders
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function getVendorOrders(PaginationQueryDto $paginationQueryDto): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        if (!$vendor) {
            throw new VendorNotFoundException();
        }

        $querryBuilder = $this->orderRepository->createQuerryBuilderForVendorAndPagination($vendor);
        $response = $this->paginationHandler->handlePagination($querryBuilder, $paginationQueryDto);

        return $response;
    }

    /**
     * Summary of getVendorOrderById
     * @param int $id
     * 
     * @return array{orderData: Order, products: array<OrderProduct>}
     */
    public function getVendorOrderById(int $id): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        if (!$vendor) {
            throw new VendorNotFoundException();
        }

        $order = $this->orderRepository->find($id);

        if (!$order) {
            throw new OrderNotFoundException();
        }

        $products = $order->getOrderProducts()->getValues();
        $products = array_filter($products, fn($item) => $item->getVendorProduct()?->getVendor()?->getId() === $vendor->getId());
        $response = ['orderData' => $order, 'products' => $products];

        return $response;
    }

    /**
     * Summary of getAllOrders
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function getAllOrders(PaginationQueryDto $paginationQueryDto): array
    {
        $querryBuilder = $this->orderRepository->createQuerryBuilderForPagination();
        $response = $this->paginationHandler->handlePagination($querryBuilder, $paginationQueryDto);

        return $response;
    }

    /**
     * Summary of patchOrder
     * @param int $id
     * @param \App\DTO\Order\PatchOrderDto $patchOrderDto
     * 
     * @throws \App\Services\Exception\NotFound\OrderNotFoundException
     * 
     * @return void
     */
    public function patchOrder(int $id, PatchOrderDto $patchOrderDto): void
    {
        $order = $this->orderRepository->find($id);

        if (!isset($order)) {
            throw new OrderNotFoundException($id);
        }

        $this->orderRepository->patch($patchOrderDto, $order);
        $this->entityManager->flush();
    }

    /**
     * Summary of cancelOrder
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\OrderNotFoundException
     * @throws \App\Services\Exception\Access\CanNotCancelOrderException
     * 
     * @return void
     */
    public function cancelOrder(int $id): void
    {
        $order = $this->orderRepository->find($id);

        if (!isset($order)) {
            throw new OrderNotFoundException($id);
        }

        $currentUser = $this->userRepository->getCurrentUser();
        $orderCustomer = $order->getCustomer();

        if ($orderCustomer === null || $currentUser->getUserIdentifier() !== $orderCustomer->getUserIdentifier()) {
            throw new CanNotCancelOrderException($id);
        }

        $this->orderRepository->cancel($order);
        $this->entityManager->flush();
    }
}
