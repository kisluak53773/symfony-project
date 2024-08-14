<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\BadRequsetException;
use Doctrine\Common\Collections\Collection;
use App\Entity\CartProduct;
use App\Entity\VendorProduct;
use App\Services\Exception\Request\NotFoundException;
use App\DTO\Cart\AddToCartDto;
use App\DTO\Cart\IncreaseDto;
use App\DTO\Cart\DecreaseDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\CartRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\VendorProductRepositoryInterface;
use App\Contract\Repository\CartProductRepositoryInterface;
use App\Contract\Service\CartServiceInterface;

class CartService implements CartServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\CartRepository $cartRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\VendorProductRepository $vendorProductRepository
     * @param \App\Repository\CartProductRepository $cartProductRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CartRepositoryInterface $cartRepository,
        private UserRepositoryInterface $userRepository,
        private VendorProductRepositoryInterface $vendorProductRepository,
        private CartProductRepositoryInterface $cartProductRepository,
    ) {}

    /**
     * Summary of createCart
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function createCart(): void
    {
        $user = $this->userRepository->getCurrentUser();

        $cart = $user->getCart();

        if (isset($cart)) {
            throw new BadRequsetException('You already have a cart');
        }

        $this->cartRepository->create($user);
        $this->entityManager->flush();
    }

    /**
     * Summary of getProductsCart
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductsCart(): Collection
    {
        $user = $this->userRepository->getCurrentUser();

        return $user->getCart()->getCartProducts();
    }

    /**
     * Summary of add
     * @param \App\DTO\Cart\AddToCartDto $addToCartDto
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return array
     */
    public function add(AddToCartDto $addToCartDto): array
    {
        $vendorProduct = $this->vendorProductRepository->find($addToCartDto->vendorProductId);

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            throw new BadRequsetException('No such item in stock');
        }

        $cartProduct = $this->cartProductRepository->findOneBy(['vendorProduct' => $vendorProduct]);

        $qunatity = $vendorProduct->getQuantity() > $addToCartDto->quantity ?
            $addToCartDto->quantity : $vendorProduct->getQuantity();

        if (isset($cartProduct)) {
            $this->cartProductRepository->increaseProductQunatity($cartProduct, $vendorProduct, $qunatity);
            $this->entityManager->flush();

            return ['responseMessage' => 'Quantity increased', 'statucCode' => 200];
        }

        $user = $this->userRepository->getCurrentUser();
        $cart = $user->getCart();

        $cartProduct = $this->cartProductRepository->create($vendorProduct, $cart, $qunatity);
        $this->entityManager->flush();

        return ['responseMessage' => ['message' => 'Product added to cart', 'id' => $cartProduct->getId()], 'statusCode' => 201];
    }

    /**
     * Summary of increase
     * @param \App\DTO\Cart\IncreaseDto $increaseDto
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return int
     */
    public function increase(IncreaseDto $increaseDto): int
    {
        $vendorProduct = $this->entityManager->getRepository(VendorProduct::class)->find($increaseDto->vendorProductId);

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            throw new BadRequsetException('No such item in stock');
        }

        $cartProduct = $this->entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NotFoundException('No such product in cart');
        }

        $quantity = $vendorProduct->getQuantity() > $increaseDto->quantity ?
            $increaseDto->quantity : $vendorProduct->getQuantity();

        $this->cartProductRepository->increaseProductQunatity($cartProduct, $vendorProduct, $quantity);
        $this->entityManager->flush();

        return $quantity;
    }

    /**
     * Summary of decrease
     * @param \App\DTO\Cart\DecreaseDto $decreaseDto
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function decrease(DecreaseDto $decreaseDto): void
    {
        $vendorProduct = $this->entityManager->getRepository(VendorProduct::class)->find($decreaseDto->vendorProductId);

        if (!isset($vendorProduct)) {
            throw new NotFoundException('This vendor does not sell this item');
        }

        $cartProduct = $this->entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NotFoundException('No such product in cart');
        }

        $this->cartProductRepository->decreaseProductQunatity($cartProduct, $vendorProduct, $decreaseDto->quantity);
        $this->entityManager->flush();
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
        $vendorProduct = $this->vendorProductRepository->find($vendorProductId);

        if (!isset($vendorProduct)) {
            throw new NotFoundException('This vendor does not sell this item');;
        }

        $cartProduct = $this->vendorProductRepository->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NotFoundException('No such product in cart');
        }

        $this->cartProductRepository->removeOne($cartProduct, $vendorProduct);
        $this->entityManager->flush();
    }

    /**
     * Summary of removeAllFromCart
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function removeAllFromCart(): void
    {
        $user = $this->userRepository->getCurrentUser();

        $cartProducts = $user->getCart()->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            throw new BadRequsetException('Your cart is empty');
        }

        $this->cartProductRepository->removeAll($cartProducts);
        $this->entityManager->flush();
    }
}
