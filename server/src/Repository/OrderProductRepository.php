<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OrderProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\CartProduct;
use App\Entity\Order;
use App\Contract\Repository\OrderProductRepositoryInteface;

/**
 * @extends ServiceEntityRepository<OrderProduct>
 */
class OrderProductRepository extends ServiceEntityRepository implements OrderProductRepositoryInteface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderProduct::class);
    }

    /**
     * Summary of create
     * @param \App\Entity\CartProduct $cartProduct
     * @param \App\Entity\Order $order
     * 
     * @return \App\Entity\OrderProduct
     */
    public function create(CartProduct $cartProduct, Order $order): OrderProduct
    {
        $orderProduct = new OrderProduct();
        $orderProduct->setOrderEntity($order);
        $orderProduct->setQuantity($cartProduct->getQuantity());
        $orderProduct->setVendorProduct($cartProduct->getVendorProduct());

        $this->getEntityManager()->persist($orderProduct);
        $this->getEntityManager()->remove($cartProduct);

        return $orderProduct;
    }

    /**
     * Add many CartProducts to the Order
     * @param CartProduct[] $cartProducts Array of CartProduct objects
     * @param Order $order The Order entity
     * 
     * @return void
     */
    public function addManyProducts(array $cartProducts, Order $order): void
    {
        foreach ($cartProducts as $cartProduct) {
            $this->create($cartProduct, $order);
        }
    }
}
