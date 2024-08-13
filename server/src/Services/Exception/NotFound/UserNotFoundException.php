<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class UserNotFoundException extends NotFoundException
{
    protected $message = 'User not found exception';
}
