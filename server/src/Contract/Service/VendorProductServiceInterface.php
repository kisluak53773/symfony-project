<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\VendorProduct\CreateVendorProductDto;
use App\DTO\VendorProduct\PatchVendorProductDto;
use App\DTO\PaginationQueryDto;
use App\Entity\VendorProduct;

interface VendorProductServiceInterface
{
    public function add(CreateVendorProductDto $createVendorProductDto): int;

    /**
     * Summary of get
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: array<VendorProduct>
     * }
     */
    public function get(PaginationQueryDto $paginationQueryDto): array;

    public function patchVendorProdut(int $id, PatchVendorProductDto $patchVendorProductDto): void;

    public function delete(int $id): void;
}
