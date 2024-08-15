<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Vendor\CreateVendorDto;
use App\DTO\Vendor\PatchVendorDto;
use App\Entity\Vendor;

interface VendorServiceInterface
{
    public function add(CreateVendorDto $createVendorDto): int;

    public function getCurrentVendor(): Vendor;

    public function patchCurrentVendor(PatchVendorDto $patchVendorDto): void;

    public function delete(int $id): void;
}
