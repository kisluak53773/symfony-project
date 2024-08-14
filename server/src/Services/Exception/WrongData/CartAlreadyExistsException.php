<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class CartAlreadyExistsException extends WrongDataException
{
    protected $message = 'You already have a cart';
}
