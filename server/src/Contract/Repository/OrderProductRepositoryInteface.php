<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Entity\CartProduct;
use App\Entity\Order;
use App\Entity\OrderProduct;

interface OrderProductRepositoryInteface
{
    public function create(CartProduct $cartProduct, Order $order): OrderProduct;

    /**
     * Add many CartProducts to the Order
     * @param CartProduct[] $cartProducts Array of CartProduct objects
     * @param Order $order The Order entity
     * 
     * @return void
     */
    public function addManyProducts(array $cartProducts, Order $order): void;
}
