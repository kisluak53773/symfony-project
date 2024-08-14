<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Entity\CartProduct;
use App\Entity\VendorProduct;
use App\Entity\Cart;

interface CartProductRepositoryInterface
{
    public function create(VendorProduct $vendorProduct, Cart $cart, int $quantity): CartProduct;

    public function increaseProductQunatity(
        CartProduct $cartProduct,
        VendorProduct $vendorProduct,
        int $quantity
    ): void;

    public function decreaseProductQunatity(
        CartProduct $cartProduct,
        VendorProduct $vendorProduct,
        int $quantity
    ): void;

    public function remove(
        CartProduct $cartProduct,
        VendorProduct $vendorProduct,
    ): void;

    /**
     * Summary of removeAll
     * @param CartProduct[] $cartProducts
     * 
     * @return void
     */
    public function removeAll(array $cartProducts): void;
}
