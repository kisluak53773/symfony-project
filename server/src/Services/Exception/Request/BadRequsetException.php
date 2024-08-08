<?php

declare(strict_types=1);

namespace App\Services\Exception\Request;

class BadRequsetException extends RequestException
{
    protected $message = 'Insuficcient data';

    protected $statusCode = 400;
}
