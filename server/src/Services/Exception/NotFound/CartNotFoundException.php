<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class CartNotFoundException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('Cart not found exception');
    }
}
