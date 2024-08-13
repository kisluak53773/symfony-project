<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class VendorProductNotFound extends NotFoundException
{
    protected $message = 'VendorProduct not found exception';
}
