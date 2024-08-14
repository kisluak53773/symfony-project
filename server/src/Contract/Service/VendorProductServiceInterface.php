<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\VendorProduct\CreateVendorProductDto;
use App\DTO\VendorProduct\PatchVendorProductDto;
use App\DTO\PaginationQueryDto;

interface VendorProductServiceInterface
{
    public function add(CreateVendorProductDto $createVendorProductDto): int;

    public function get(PaginationQueryDto $paginationQueryDto): array;

    public function patchVendorProdut(int $id, PatchVendorProductDto $patchVendorProductDto): void;

    public function delete(int $id): void;
}
