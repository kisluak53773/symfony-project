<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Entity\Cart;
use App\Services\Exception\Request\BadRequsetException;
use Doctrine\Common\Collections\Collection;
use App\Entity\CartProduct;
use App\Entity\VendorProduct;
use App\Services\Exception\Request\NotFoundException;
use App\DTO\Cart\AddToCartDto;
use App\DTO\Cart\IncreaseDto;
use App\DTO\Cart\DecreaseDto;

class CartService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Security $security,
    ) {}

    /**
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function createCart(): void
    {
        $entityManager = $this->registry->getManager();

        $userPhoen = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhoen]);

        $cart = $user->getCart();

        if (isset($cart)) {
            throw new BadRequsetException('You already have a cart');
        }

        $cart = new Cart();
        $cart->setCustomer($user);

        $entityManager->persist($cart);
        $entityManager->flush();
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getProductsCart(): Collection
    {
        $entityManager = $this->registry->getManager();

        $userPhoen = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhoen]);

        return $user->getCart()->getCartProducts();
    }

    /**
     * Summary of addToCart
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return array
     */
    public function addToCart(AddToCartDto $addToCartDto): array
    {
        $entityManager = $this->registry->getManager();

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($addToCartDto->vendorProductId);
        $qunatity = $vendorProduct->getQuantity() > $addToCartDto->quantity ?
            $addToCartDto->quantity : $vendorProduct->getQuantity();

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            throw new BadRequsetException('No such item in stock');
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (isset($cartProduct)) {
            $cartProduct->increaseQuantity($qunatity);
            $vendorProduct->decreaseQuantity($qunatity);

            $entityManager->persist($cartProduct);
            $entityManager->persist($vendorProduct);
            $entityManager->flush();

            return ['responseMessage' => 'Quantity increased', 'statucCode' => 200];
        }


        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $cart = $user->getCart();

        $cartProduct = new CartProduct();
        $cartProduct->setCart($cart);
        $cartProduct->setVendorProduct($vendorProduct);
        $cartProduct->setQuantity($qunatity);
        $vendorProduct->decreaseQuantity($qunatity);

        $entityManager->persist($cartProduct);
        $entityManager->persist($vendorProduct);
        $entityManager->flush();

        return ['responseMessage' => ['message' => 'Product added to cart', 'id' => $cartProduct->getId()], 'statusCode' => 201];
    }

    /**
     * Summary of increaseProductAmount
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return int
     */
    public function increaseProductAmount(IncreaseDto $increaseDto): int
    {
        $entityManager = $this->registry->getManager();

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($increaseDto->vendorProductId);

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            throw new BadRequsetException('No such item in stock');
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NotFoundException('No such product in cart');
        }

        $quantity = $vendorProduct->getQuantity() > $increaseDto->quantity ?
            $increaseDto->quantity : $vendorProduct->getQuantity();

        $cartProduct->increaseQuantity($quantity);
        $vendorProduct->decreaseQuantity($quantity);

        $entityManager->persist($cartProduct);
        $entityManager->persist($vendorProduct);
        $entityManager->flush();

        return $quantity;
    }

    /**
     * Summary of decreaseProductAmount
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function decreaseProductAmount(DecreaseDto $decreaseDto): void
    {
        $entityManager = $this->registry->getManager();

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($decreaseDto->vendorProductId);

        if (!isset($vendorProduct)) {
            throw new NotFoundException('This vendor does not sell this item');
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NotFoundException('No such product in cart');
        }

        if ($cartProduct->getQuantity() > $decreaseDto->quantity) {
            $cartProduct->decreaseQuantity($decreaseDto->quantity);
            $vendorProduct->increaseQuantity($decreaseDto->quantity);

            $entityManager->persist($cartProduct);
            $entityManager->persist($vendorProduct);
        } else {
            $vendorProduct->increaseQuantity($cartProduct->getQuantity());

            $entityManager->persist($vendorProduct);
            $entityManager->remove($cartProduct);
        }

        $entityManager->flush();
    }

    /**
     * Summary of removeFromCart
     * @param int $vendorProductId
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function removeFromCart(int $vendorProductId): void
    {
        $entityManager = $this->registry->getManager();
        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($vendorProductId);

        if (!isset($vendorProduct)) {
            throw new NotFoundException('This vendor does not sell this item');;
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NotFoundException('No such product in cart');
        }

        $vendorProduct->increaseQuantity($cartProduct->getQuantity());

        $entityManager->persist($vendorProduct);
        $entityManager->remove($cartProduct);
        $entityManager->flush();
    }

    /**
     * Summary of removeAllFromCart
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function removeAllFromCart(): void
    {
        $entityManager = $this->registry->getManager();
        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $cartProducts = $user->getCart()->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            throw new BadRequsetException('Your cart is empty');
        }

        foreach ($cartProducts as $cartProduct) {
            $entityManager->remove($cartProduct);
        }

        $entityManager->flush();
    }
}
