<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Product\CreateProductDto;
use App\DTO\Product\ProductSearchParamsDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ProductServiceIntrrafce
{
    public function addWithVendor(UploadedFile $image, CreateProductDto $createProductDto): int;

    public function list(ProductSearchParamsDto $productSearchParamsDto): array;

    public function getProductsVendorDoesNotSell(ProductSearchParamsDto $productSearchParamsDto): array;

    public function delete(int $id): void;
}
