<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Entity\Product;
use App\DTO\VendorProduct\PatchVendorProductDto;
use App\Entity\VendorProduct;
use App\Entity\Vendor;
use Doctrine\ORM\QueryBuilder;

interface VendorProductRepositoryInterface
{
    public function createQueryBuilderForPaginationWithVendor(Vendor $vendor): QueryBuilder;

    public function create(
        Vendor $vendor,
        Product $product,
        string $price,
        int $quantity = null
    ): VendorProduct;

    public function patch(PatchVendorProductDto $patchVendorProductDto, VendorProduct $vendorProduct): void;

    public function remove(VendorProduct $vendorProduct): void;
}
