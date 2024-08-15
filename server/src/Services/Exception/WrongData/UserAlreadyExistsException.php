<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class UserAlreadyExistsException extends WrongDataException
{
    public function __construct(string $phone)
    {
        parent::__construct("User with phone $phone already exists");
    }
}
