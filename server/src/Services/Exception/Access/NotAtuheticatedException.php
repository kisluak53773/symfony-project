<?php

declare(strict_types=1);

namespace App\Services\Exception\Access;

class NotAtuheticatedException extends AccessForbiddenException
{
    public function __construct()
    {
        parent::__construct('You are not authenticated');
    }
}
