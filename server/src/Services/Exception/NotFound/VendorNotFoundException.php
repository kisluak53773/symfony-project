<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class VendorNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Vendor with id $id not found");
    }
}
