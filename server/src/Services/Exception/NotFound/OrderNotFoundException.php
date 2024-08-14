<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class OrderNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Order with id $id not found");
    }
}
