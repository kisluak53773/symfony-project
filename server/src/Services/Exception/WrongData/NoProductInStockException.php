<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class NoProductInStockException extends WrongDataException
{
    public function __construct(int $vendorProductId)
    {
        parent::__construct("No such item with id $vendorProductId in stock");
    }
}
