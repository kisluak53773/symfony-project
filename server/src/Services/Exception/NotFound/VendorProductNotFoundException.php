<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class VendorProductNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Vendor product with id $id not found");
    }
}
