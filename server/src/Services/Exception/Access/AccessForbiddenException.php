<?php

declare(strict_types=1);

namespace App\Services\Exception\Access;

use Exception;

class AccessForbiddenException extends Exception
{
    public function __construct(string $message = 'Default forbidden exception', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
