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
use App\DTO\Product\CreateProductDto;
use App\Entity\Producer;
use App\Entity\Type;
use App\Contract\Repository\ProductRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    public function __construct(private PaginatedFinderInterface $productFinder, ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Summary of createQueryBuilderForPagination
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilderForPagination(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.id', 'ASC');
    }

    /**
     * Summary of findAllProductsExcludingVendor
     * @param \App\Entity\Vendor $vendor
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllProductsExcludingVendor(Vendor $vendor): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.vendorProducts', 'vp')
            ->leftJoin('vp.vendor', 'v')
            ->where('v.id IS NULL OR v.id != :vendorId')
            ->setParameter('vendorId', $vendor->getId());;
    }

    /**
     * Summary of searchByTitle
     * @param \App\DTO\Product\ProductSearchParamsDto $productSearchParamsDto
     * @param int $vendorId
     * 
     * @return \FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface
     */
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

    /**
     * Summary of create
     * @param \App\DTO\Product\CreateProductDto $createProductDto
     * @param \App\Entity\Type $type
     * @param \App\Entity\Producer $producer
     * @param string $imagePath
     * 
     * @return \App\Entity\Product
     */
    public function create(
        CreateProductDto $createProductDto,
        Type $type,
        Producer $producer,
        string $imagePath
    ): Product {
        $product = new Product();
        $product->setTitle($createProductDto->title);
        $product->setDescription($createProductDto->description);
        $product->setCompound($createProductDto->compound);
        $product->setStorageConditions($createProductDto->storageConditions);
        $product->setWeight($createProductDto->weight);
        $product->setImage($imagePath);
        $product->setType($type);
        $product->setProducer($producer);
        $this->getEntityManager()->persist($product);

        return $product;
    }

    public function remove(Product $product): void
    {
        $this->getEntityManager()->remove($product);
    }
}
