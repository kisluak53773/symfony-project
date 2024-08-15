<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CartProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Cart;
use App\Entity\VendorProduct;
use App\Contract\Repository\CartProductRepositoryInterface;

/**
 * @extends ServiceEntityRepository<CartProduct>
 */
class CartProductRepository extends ServiceEntityRepository implements CartProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CartProduct::class);
    }

    /**
     * Summary of create
     * @param \App\Entity\VendorProduct $vendorProduct
     * @param \App\Entity\Cart $cart
     * @param int $quantity
     * 
     * @return \App\Entity\CartProduct
     */
    public function create(VendorProduct $vendorProduct, Cart $cart, int $quantity): CartProduct
    {
        $cartProduct = new CartProduct();
        $cartProduct->setCart($cart);
        $cartProduct->setVendorProduct($vendorProduct);
        $cartProduct->setQuantity($quantity);
        $vendorProduct->decreaseQuantity($quantity);

        $this->getEntityManager()->persist($cartProduct);
        $this->getEntityManager()->persist($vendorProduct);

        return $cartProduct;
    }

    /**
     * Summary of increaseProductQunatity
     * @param \App\Entity\CartProduct $cartProduct
     * @param \App\Entity\VendorProduct $vendorProduct
     * @param int $quantity
     * 
     * @return void
     */
    public function increaseProductQunatity(
        CartProduct $cartProduct,
        VendorProduct $vendorProduct,
        int $quantity
    ): void {
        $cartProduct->increaseQuantity($quantity);
        $vendorProduct->decreaseQuantity($quantity);

        $this->getEntityManager()->persist($cartProduct);
        $this->getEntityManager()->persist($vendorProduct);
    }

    /**
     * Summary of decreaseProductQunatity
     * @param \App\Entity\CartProduct $cartProduct
     * @param \App\Entity\VendorProduct $vendorProduct
     * @param int $quantity
     * 
     * @return void
     */
    public function decreaseProductQunatity(
        CartProduct $cartProduct,
        VendorProduct $vendorProduct,
        int $quantity
    ): void {
        if ($cartProduct->getQuantity() > $quantity) {
            $cartProduct->decreaseQuantity($quantity);
            $vendorProduct->increaseQuantity($quantity);

            $this->getEntityManager()->persist($cartProduct);
            $this->getEntityManager()->persist($vendorProduct);
        } else {
            $vendorProduct->increaseQuantity($cartProduct->getQuantity() ? $cartProduct->getQuantity() : 0);

            $this->getEntityManager()->persist($vendorProduct);
            $this->getEntityManager()->remove($cartProduct);
        }
    }

    /**
     * Summary of remove
     * @param \App\Entity\CartProduct $cartProduct
     * @param \App\Entity\VendorProduct $vendorProduct
     * 
     * @return void
     */
    public function remove(
        CartProduct $cartProduct,
        VendorProduct $vendorProduct,
    ): void {
        $vendorProduct->increaseQuantity($cartProduct->getQuantity() ? $cartProduct->getQuantity() : 0);
        $this->getEntityManager()->persist($vendorProduct);
        $this->getEntityManager()->remove($cartProduct);
    }

    /**
     * Summary of removeAll
     * @param CartProduct[] $cartProducts
     * 
     * @return void
     */
    public function removeAll(array $cartProducts): void
    {
        foreach ($cartProducts as $cartProduct) {
            $vendorProduct = $cartProduct->getVendorProduct();
            if ($vendorProduct instanceof VendorProduct) {
                $this->remove($cartProduct, $vendorProduct);
            } else {
                throw new \InvalidArgumentException('Expected instance of VendorProduct, got something else.');
            }
        }
    }
}
