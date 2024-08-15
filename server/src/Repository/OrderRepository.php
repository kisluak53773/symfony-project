<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use App\Entity\User;
use App\Entity\Vendor;
use App\DTO\Order\CreateOrderDto;
use DateTime;
use App\Enum\OrderStatus;
use App\DTO\Order\PatchOrderDto;
use App\Contract\Repository\OrderRepositoryInterface;
use App\Services\Exception\WrongData\WrongDateFormmatException;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * Summary of createQuerryBuilderForPagination
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQuerryBuilderForPagination(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'ASC');
    }

    /**
     * Summary of getAllOrdersBelonignToUser
     * @param \App\Entity\User $user
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getAllOrdersBelonignToUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->where('o.customer = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC');
    }


    /**
     * Summary of create
     * @param \App\DTO\Order\CreateOrderDto $createOrderDto
     * @param \App\Entity\User $user
     * 
     * @return \App\Entity\Order
     */
    public function create(CreateOrderDto $createOrderDto, User $user): Order
    {
        $deliveryDate = DateTime::createFromFormat('Y-m-d\TH:i', $createOrderDto->deliveryTime);

        if (!$deliveryDate) {
            throw new WrongDateFormmatException();
        }

        $order = new Order();
        $order->setCustomer($user);
        $order->setPaymentMethod($createOrderDto->paymentMethod);
        $order->setDeliveryTime($deliveryDate);
        $order->setOrderStatus(OrderStatus::ORDER_PROCESSED->value);

        if (isset($createOrderDto->comment)) {
            $order->setComment($createOrderDto->comment);
        }

        $this->getEntityManager()->persist($order);

        return $order;
    }


    /**
     * Summary of patch
     * @param \App\DTO\Order\PatchOrderDto $patchOrderDto
     * @param \App\Entity\Order $order
     * 
     * @return void
     */
    public function patch(PatchOrderDto $patchOrderDto, Order $order): void
    {
        $deliveryDate = DateTime::createFromFormat('Y-m-d\TH:i', $patchOrderDto->deliveryTime);

        if (!$deliveryDate) {
            throw new WrongDateFormmatException();
        }

        $order->setPaymentMethod($patchOrderDto->paymentMethod);
        $order->setOrderStatus($patchOrderDto->orderStatus);
        $order->setDeliveryTime($deliveryDate);
        $this->getEntityManager()->persist($order);
    }

    /**
     * Summary of cancel
     * @param \App\Entity\Order $order
     * @return void
     */
    public function cancel(Order $order): void
    {
        $order->setOrderStatus(OrderStatus::ORDER_CANCELED->value);
        $this->getEntityManager()->persist($order);
    }

    /**
     * Summary of createQuerryBuilderForVendorAndPagination
     * @param \App\Entity\Vendor $vendor
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQuerryBuilderForVendorAndPagination(Vendor $vendor): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.orderProducts', 'op')
            ->innerJoin('op.vendorProduct', 'vp')
            ->where('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('o.createdAt', 'DESC');
    }
}
