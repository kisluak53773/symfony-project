<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\ForbiddenException;
use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\OrderRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\OrderProductRepositoryInteface;
use App\Contract\Service\OrderServiceInterface;
use App\Contract\PaginationHandlerInterface;

class OrderService implements OrderServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Services\PaginationHandler $paginationHandler
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
     * Summary of index
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function createOrder(CreateOrderDto $createOrderDto): void
    {
        $user = $this->userRepository->getCurrentUser();
        $cart = $user->getCart();

        if (!isset($cart)) {
            throw new NotFoundException('You do not have a cart');
        }

        $order = $this->orderRepository->create($createOrderDto, $user);
        $cartProducts = $cart->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            throw new BadRequsetException('Your cart is empty');
        }

        $this->orderProductRepository->addManyProducts($cartProducts, $order);
        $this->entityManager->flush();
    }

    /**
     * Summary of getUserOrders
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return array
     */
    public function getVendorOrders(PaginationQueryDto $paginationQueryDto): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        if (!isset($vendor)) {
            throw new NotFoundException('Vendor data is not found');
        }

        $querryBuilder = $this->orderRepository->createQuerryBuilderForVendorAndPagination($vendor);
        $response = $this->paginationHandler->handlePagination($querryBuilder, $paginationQueryDto);

        return $response;
    }

    /**
     * Summary of getVendorOrderById
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return array
     */
    public function getVendorOrderById(int $id): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        if (!isset($vendor)) {
            throw new NotFoundException('Vendor data is not found');
        }

        $order = $this->orderRepository->find($id);
        $products = $order->getOrderProducts()->getValues();

        $products = array_filter($products, fn($item) => $item->getVendorProduct()->getVendor()->getId() === $vendor->getId());
        $response = ['orderData' => $order, 'products' => $products];

        return $response;
    }

    /**
     * Summary of getAllOrders
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function patchOrder(int $id, PatchOrderDto $patchOrderDto): void
    {
        $order = $this->orderRepository->find($id);

        if (!isset($order)) {
            throw new NotFoundException('Such order does not exist');
        }

        $this->orderRepository->patch($patchOrderDto, $order);
        $this->entityManager->flush();
    }

    /**
     * Summary of cancelOrder
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * @throws \App\Services\Exception\Request\ForbiddenException
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function cancelOrder(int $id): void
    {
        $order = $this->orderRepository->find($id);

        if (!isset($order)) {
            throw new NotFoundException('Such order does not exist');
        }

        if ($this->userRepository->getCurrentUser()->getUserIdentifier() !== $order->getCustomer()->getUserIdentifier()) {
            throw new ForbiddenException('You can not cancel this order');
        }

        $this->orderRepository->cancel($order);
        $this->entityManager->flush();
    }
}
