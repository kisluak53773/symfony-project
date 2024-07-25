<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Vendor;
use Doctrine\ORM\QueryBuilder;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Symfony\Component\HttpFoundation\Request;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;


/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(private PaginatedFinderInterface $productFinder, ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function createQueryBuilderForPagination(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC');
    }

    public function findAllProductsExcludingVendor(Vendor $vendor): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.vendorProducts', 'vp')
            ->leftJoin('vp.vendor', 'v')
            ->where('v.id IS NULL OR v.id != :vendorId')
            ->setParameter('vendorId', $vendor->getId());;
    }

    public function searchByTitle(Request $request): PaginatorAdapterInterface
    {
        $title = $request->query->get('title', '');
        $query = new Query();
        $boolQuery = new BoolQuery();

        if ($title) {
            $queryString = new QueryString();
            $queryString->setQuery('*' . $title . '*');
            $queryString->setDefaultField('title');

            $boolQuery->addMust($queryString);
        }

        $query->setQuery($boolQuery);
        $query->setSort([
            'vendorProducts.price' => [
                'order' => 'desc',
                'nested' => [
                    'path' => 'vendorProducts',
                ],
            ],
        ]);

        $results = $this->productFinder->createPaginatorAdapter($query);

        return $results;
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
