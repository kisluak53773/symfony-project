<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Common\Collections\Collection;
use App\DTO\Cart\AddToCartDto;
use App\DTO\Cart\IncreaseDto;
use App\DTO\Cart\DecreaseDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\CartRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\VendorProductRepositoryInterface;
use App\Contract\Repository\CartProductRepositoryInterface;
use App\Contract\Service\CartServiceInterface;
use App\Services\Exception\WrongData\CartAlreadyExistsException;
use App\Services\Exception\WrongData\NoProductInStockException;
use App\Services\Exception\NotFound\NoProductInCartException;
use App\Services\Exception\WrongData\CartIsEmptyException;
use App\Entity\CartProduct;

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
     * @throws \App\Services\Exception\WrongData\CartAlreadyExistsException
     * 
     * @return void
     */
    public function createCart(): void
    {
        $user = $this->userRepository->getCurrentUser();

        $cart = $user->getCart();

        if (isset($cart)) {
            throw new CartAlreadyExistsException();
        }

        $this->cartRepository->create($user);
        $this->entityManager->flush();
    }

    /**
     * Summary of getProductsCart
     * @return Collection<int, CartProduct>
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
     * @throws \App\Services\Exception\WrongData\NoProductInStockException
     * 
     * @return array
     */
    public function add(AddToCartDto $addToCartDto): array
    {
        $vendorProduct = $this->vendorProductRepository->find($addToCartDto->vendorProductId);

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            throw new NoProductInStockException($addToCartDto->vendorProductId);
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
     * @throws \App\Services\Exception\WrongData\NoProductInStockException
     * @throws \App\Services\Exception\NotFound\NoProductInCartException
     * 
     * @return int
     */
    public function increase(IncreaseDto $increaseDto): int
    {
        $vendorProduct = $this->vendorProductRepository->find($increaseDto->vendorProductId);

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            throw new NoProductInStockException($increaseDto->vendorProductId);
        }

        $cartProduct = $this->cartProductRepository->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NoProductInCartException($increaseDto->vendorProductId);
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
     * @throws \App\Services\Exception\WrongData\NoProductInStockException
     * @throws \App\Services\Exception\NotFound\NoProductInCartException
     * 
     * @return void
     */
    public function decrease(DecreaseDto $decreaseDto): void
    {
        $vendorProduct = $this->vendorProductRepository->find($decreaseDto->vendorProductId);

        if (!isset($vendorProduct)) {
            throw new NoProductInStockException($decreaseDto->vendorProductId);
        }

        $cartProduct = $this->cartProductRepository->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NoProductInCartException($decreaseDto->vendorProductId);
        }

        $this->cartProductRepository->decreaseProductQunatity($cartProduct, $vendorProduct, $decreaseDto->quantity);
        $this->entityManager->flush();
    }

    /**
     * Summary of removeFromCart
     * @param int $vendorProductId
     * 
     * @throws \App\Services\Exception\WrongData\NoProductInStockException
     * @throws \App\Services\Exception\NotFound\NoProductInCartException
     * 
     * @return void
     */
    public function removeFromCart(int $vendorProductId): void
    {
        $vendorProduct = $this->vendorProductRepository->find($vendorProductId);

        if (!isset($vendorProduct)) {
            throw new NoProductInStockException($vendorProductId);
        }

        $cartProduct = $this->vendorProductRepository->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            throw new NoProductInCartException($vendorProductId);
        }

        $this->cartProductRepository->removeOne($cartProduct, $vendorProduct);
        $this->entityManager->flush();
    }

    /**
     * Summary of removeAllFromCart
     * @throws \App\Services\Exception\WrongData\CartIsEmptyException
     * 
     * @return void
     */
    public function removeAllFromCart(): void
    {
        $user = $this->userRepository->getCurrentUser();

        $cartProducts = $user->getCart()->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            throw new CartIsEmptyException();
        }

        $this->cartProductRepository->removeAll($cartProducts);
        $this->entityManager->flush();
    }
}
