<?php

namespace App\Repository;

use App\Entity\VendorProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Vendor;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<VendorProduct>
 */
class VendorProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, VendorProduct::class);
    }

    public function createQueryBuilderForPaginationWithVendor(Vendor $vendor): QueryBuilder
    {
        return $this->createQueryBuilder('vp')
            ->where('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('vp.id', 'ASC');
    }

    //    /**
    //     * @return VendorProduct[] Returns an array of VendorProduct objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('v.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?VendorProduct
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
