<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use App\Entity\User;
use App\Entity\Vendor;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function createQuerryBuilderForPagination(): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'ASC');
    }

    public function getAllOrdersBelonignToUser(User $user): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->where('o.customer = :user')
            ->setParameter('user', $user)
            ->orderBy('o.createdAt', 'DESC');
    }

    public function createQuerryBuilderForVendorAndPagination(Vendor $vendor): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->innerJoin('o.orderProducts', 'op')
            ->innerJoin('op.vendorProduct', 'vp')
            ->where('vp.vendor = :vendor')
            ->setParameter('vendor', $vendor)
            ->orderBy('o.createdAt', 'DESC');
    }

    //    /**
    //     * @return Order[] Returns an array of Order objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Order
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
