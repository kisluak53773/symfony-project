<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class ProductNotFoundException extends NotFoundException
{
    public function __construct(int $productId)
    {
        parent::__construct("Product with id $productId not in cart");
    }
}
