<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class CartIsEmptyException extends WrongDataException
{
    public function __construct()
    {
        parent::__construct('Your cart is empty');
    }
}
