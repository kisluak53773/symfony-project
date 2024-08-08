<?php

declare(strict_types=1);

namespace App\Services\Exception\Request;

class ServerErrorException extends RequestException
{
    protected $message = 'Something went wrong';

    protected $statusCode = 500;
}
