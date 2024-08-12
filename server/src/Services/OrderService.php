<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Entity\OrderProduct;
use App\Entity\Order;
use App\Enum\OrderStatus;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\OrderRepository;
use DateTime;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\ForbiddenException;
use App\DTO\Order\CreateOrderDto;
use App\DTO\Order\PatchOrderDto;
use App\DTO\PaginationQueryDto;

class OrderService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Security $security,
        private PaginatorInterface $paginator,
        private OrderRepository $orderRepository,
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
    public function createOrderDto(CreateOrderDto $createOrderDto): void
    {
        $entityManager = $this->registry->getManager();

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $deliveryDate = DateTime::createFromFormat('Y-m-d\TH:i', $createOrderDto->deliveryTime);

        $order = new Order();
        $order->setCustomer($user);
        $order->setPaymentMethod($createOrderDto->paymentMethod);
        $order->setDeliveryTime($deliveryDate);
        $order->setOrderStatus(OrderStatus::ORDER_PROCESSED->value);

        if (isset($createOrderDto->comment)) {
            $order->setComment($createOrderDto->comment);
        }

        $entityManager->persist($order);

        $cart = $user->getCart();

        if (!isset($cart)) {
            throw new NotFoundException('You do not have a cart');
        }

        $cartProducts = $cart->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            throw new BadRequsetException('Your cart is empty');
        }

        foreach ($cartProducts as $cartProduct) {
            $orderProduct = new OrderProduct();
            $orderProduct->setOrderEntity($order);
            $orderProduct->setQuantity($cartProduct->getQuantity());
            $orderProduct->setVendorProduct($cartProduct->getVendorProduct());

            $entityManager->persist($orderProduct);
            $entityManager->remove($cartProduct);
        }

        $entityManager->flush();
    }

    /**
     * Summary of getUserOrders
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
     */
    public function getUserOrders(PaginationQueryDto $paginationQueryDto): array
    {
        $entityManager = $this->registry->getManager();
        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $querryBuilder = $this->orderRepository->getAllOrdersBelonignToUser($user);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $paginationQueryDto->page,
            $paginationQueryDto->limit,
        );

        $orders = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $orders,
        ];

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
        $entityManager = $this->registry->getManager();
        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $vendor = $user->getVendor();

        if (!isset($vendor)) {
            throw new NotFoundException('Vendor data is not found');
        }

        $querryBuilder = $this->orderRepository->createQuerryBuilderForVendorAndPagination($vendor);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $paginationQueryDto->page,
            $paginationQueryDto->limit,
        );

        $orders = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $orders,
        ];

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
        $entityManager = $this->registry->getManager();
        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $vendor = $user->getVendor();

        if (!isset($vendor)) {
            throw new NotFoundException('Vendor data is not found');
        }

        $order = $entityManager->getRepository(Order::class)->find($id);
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

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $paginationQueryDto->page,
            $paginationQueryDto->limit,
        );

        $orders = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $orders,
        ];

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
        $entityManager = $this->registry->getManager();
        $order = $entityManager->getRepository(Order::class)->find($id);

        if (!isset($order)) {
            throw new NotFoundException('Such order does not exist');
        }

        $deliveryDate = DateTime::createFromFormat('Y-m-d\TH:i', $patchOrderDto->deliveryTime);

        $order->setPaymentMethod($patchOrderDto->paymentMethod);
        $order->setOrderStatus($patchOrderDto->orderStatus);
        $order->setDeliveryTime($deliveryDate);
        $entityManager->persist($order);

        $entityManager->flush();
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
        $entityManager = $this->registry->getManager();
        $order = $entityManager->getRepository(Order::class)->find($id);

        if (!isset($order)) {
            throw new NotFoundException('Such order does not exist');
        }

        if ($this->security->getUser()->getUserIdentifier() !== $order->getCustomer()->getUserIdentifier()) {
            throw new ForbiddenException('You can not cancel this order');
        }

        $order->setOrderStatus(OrderStatus::ORDER_CANCELED->value);
        $entityManager->persist($order);

        $entityManager->flush();
    }
}
