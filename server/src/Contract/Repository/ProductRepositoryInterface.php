<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\DTO\Product\CreateProductDto;
use App\Entity\Producer;
use App\Entity\Type;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Vendor;
use App\DTO\Product\ProductSearchParamsDto;
use FOS\ElasticaBundle\Paginator\PaginatorAdapterInterface;
use App\Entity\Product;

interface ProductRepositoryInterface
{
    public function createQueryBuilderForPagination(): QueryBuilder;

    public function findAllProductsExcludingVendor(Vendor $vendor): QueryBuilder;

    public function searchByTitle(
        ProductSearchParamsDto $productSearchParamsDto,
        int $vendorId = null,
    ): PaginatorAdapterInterface;

    public function create(
        CreateProductDto $createProductDto,
        Type $type,
        Producer $producer,
        string $imagePath
    ): Product;

    public function remove(Product $product): void;
}
