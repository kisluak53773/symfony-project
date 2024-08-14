<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Product\CreateProductDto;
use App\DTO\Product\ProductSearchParamsDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ProductServiceIntrrafce
{
    public function addWithVendor(UploadedFile $image, CreateProductDto $createProductDto): int;

    /**
     * Summary of list
     * @param \App\DTO\Product\ProductSearchParamsDto $productSearchParamsDto
     * 
     * @return array{total_items: int, current_page: int, total_pages: int, data: array}
     */
    public function list(ProductSearchParamsDto $productSearchParamsDto): array;

    /**
     * Summary of getProductsVendorDoesNotSell
     * @param \App\DTO\Product\ProductSearchParamsDto $productSearchParamsDto
     * 
     * @return array{total_items: int, current_page: int, total_pages: int, data: array}
     */
    public function getProductsVendorDoesNotSell(ProductSearchParamsDto $productSearchParamsDto): array;

    public function delete(int $id): void;
}
