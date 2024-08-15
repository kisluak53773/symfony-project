<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class UserNotFoundByIdentifierException extends NotFoundException
{
    public function __construct()
    {
        parent::__construct('User not found by identifier');
    }
}
