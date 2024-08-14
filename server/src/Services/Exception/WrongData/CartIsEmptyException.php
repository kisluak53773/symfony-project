<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class CartIsEmptyException extends WrongDataException
{
    protected $message = 'Your cart is empty';
}
