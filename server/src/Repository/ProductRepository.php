<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Vendor;
use Doctrine\ORM\QueryBuilder;
use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Query\QueryString;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use Elastica\Query\Term;
use Elastica\Query\Nested;
use App\DTO\Product\ProductSearchParamsDto;


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

    public function searchByTitle(
        ProductSearchParamsDto $productSearchParamsDto,
        int $vendorId = null,
    ): PaginatorAdapterInterface {
        $query = new Query();
        $boolQuery = new BoolQuery();

        if ($productSearchParamsDto->title) {
            $queryString = new QueryString();
            $queryString->setQuery('*' . $productSearchParamsDto->title . '*');
            $queryString->setDefaultField('title');

            $boolQuery->addMust($queryString);
        }

        if (isset($vendorId)) {
            $nestedBoolQuery = new BoolQuery();
            $termQuery = new Term(['vendorProducts.vendorId' => $vendorId]);
            $nestedBoolQuery->addMust($termQuery);

            $nestedQuery = new Nested();
            $nestedQuery->setPath('vendorProducts');

            $nestedQuery->setQuery((new BoolQuery())->addMustNot($nestedBoolQuery));

            $boolQuery->addFilter($nestedQuery);
        }

        if (count($productSearchParamsDto->types) > 0) {
            $typesBool = new BoolQuery();
            foreach ($productSearchParamsDto->types as $type) {
                $typesTerm = new Term(['typeId' => $type]);
                $typesBool->addShould($typesTerm);
            }
            $boolQuery->addFilter($typesBool);
        }

        if (count($productSearchParamsDto->types) > 0) {
            $producersBool = new BoolQuery();
            foreach ($productSearchParamsDto->types as $producer) {
                $producerTerm = new Term(['typeId' => $producer]);
                $producersBool->addShould($producerTerm);
            }
            $boolQuery->addFilter($producersBool);
        }

        $query->setQuery($boolQuery);

        if (isset($productSearchParamsDto->priceSort)) {
            $query->setSort([
                'vendorProducts.price' => [
                    'order' => $productSearchParamsDto->priceSort,
                    'nested' => [
                        'path' => 'vendorProducts',
                    ],
                ],
            ]);
        }

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
