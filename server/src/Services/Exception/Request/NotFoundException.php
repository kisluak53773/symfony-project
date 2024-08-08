<?php

declare(strict_types=1);

namespace App\Services\Exception\Request;

class NotFoundException extends RequestException
{
    protected $message = 'Not found';

    protected $statusCode = 400;
}
