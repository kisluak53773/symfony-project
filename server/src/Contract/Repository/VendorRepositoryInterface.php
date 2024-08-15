<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\DTO\Vendor\PatchVendorDto;
use App\Entity\Vendor;
use App\Entity\User;
use App\DTO\Vendor\CreateVendorDto;

interface VendorRepositoryInterface
{
    public function create(CreateVendorDto $createVendorDto, User $user): Vendor;

    public function patch(PatchVendorDto $patchVendorDto, Vendor $vendor): void;

    public function remove(Vendor $vendor): void;
}
