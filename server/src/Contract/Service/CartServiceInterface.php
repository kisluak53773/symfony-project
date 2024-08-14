<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Cart\AddToCartDto;
use App\DTO\Cart\IncreaseDto;
use App\DTO\Cart\DecreaseDto;
use Doctrine\Common\Collections\Collection;
use App\Entity\CartProduct;


interface CartServiceInterface
{
    public function createCart(): void;

    /**
     * Summary of getProductsCart
     * @return Collection<int, CartProduct>
     */
    public function getProductsCart(): Collection;

    public function add(AddToCartDto $addToCartDto): array;

    public function increase(IncreaseDto $increaseDto): int;

    public function decrease(DecreaseDto $decreaseDto): void;

    public function removeFromCart(int $vendorProductId): void;

    public function removeAllFromCart(): void;
}
