<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class UserNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("User with id $id not found");
    }
}
