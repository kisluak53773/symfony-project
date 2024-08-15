<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class CartAlreadyExistsException extends WrongDataException
{

    public function __construct()
    {
        parent::__construct('You already have a cart');
    }
}
