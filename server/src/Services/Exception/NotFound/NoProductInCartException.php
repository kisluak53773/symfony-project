<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class NoProductInCartException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Product with id $id not in cart");
    }
}
