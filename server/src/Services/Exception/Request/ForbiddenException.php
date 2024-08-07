<?php

declare(strict_types=1);

namespace App\Services\Exception\Request;

class ForbiddenException extends RequestException
{
    protected $message = 'Access denied';

    protected $statusCode = 403;
}
