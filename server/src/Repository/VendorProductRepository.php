<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\VendorProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Vendor;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Product;
use App\DTO\VendorProduct\PatchVendorProductDto;
use App\Contract\Repository\VendorProductRepositoryInterface;

/**
 * @extends ServiceEntityRepository<VendorProduct>
 */
class VendorProductRepository extends ServiceEntityRepository implements VendorProductRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorProduct::class);
    }

    /**
     * Summary of createQueryBuilderForPaginationWithVendor
     * @param \App\Entity\Vendor $vendor
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilderForPaginationWithVendor(Vendor $vendor): QueryBuilder
    {
        return $this->createQueryBuilder('vp')
            ->where('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('vp.id', 'ASC');
    }

    /**
     * Summary of create
     * @param \App\Entity\Vendor $vendor
     * @param \App\Entity\Product $product
     * @param string $price
     * @param int $quantity
     * 
     * @return \App\Entity\VendorProduct
     */
    public function create(
        Vendor $vendor,
        Product $product,
        string $price,
        int $quantity = null
    ): VendorProduct {
        $vendorProduct = new VendorProduct();
        $vendorProduct->setVendor($vendor);
        $vendorProduct->setProduct($product);
        $vendorProduct->setPrice($price);

        if ($quantity) {
            $vendorProduct->setQuantity($quantity);
        }

        $this->getEntityManager()->persist($vendorProduct);

        return $vendorProduct;
    }

    public function patch(PatchVendorProductDto $patchVendorProductDto, VendorProduct $vendorProduct): void
    {
        $vendorProduct->setPrice($patchVendorProductDto->price);
        $vendorProduct->setQuantity($patchVendorProductDto->quantity);

        $this->getEntityManager()->persist($vendorProduct);
    }

    /**
     * Summary of remove
     * @param \App\Entity\VendorProduct $vendorProduct
     * 
     * @return void
     */
    public function remove(VendorProduct $vendorProduct): void
    {
        $this->getEntityManager()->remove($vendorProduct);
    }
}
