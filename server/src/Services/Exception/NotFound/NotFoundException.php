<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $message = 'Default not found exception', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
