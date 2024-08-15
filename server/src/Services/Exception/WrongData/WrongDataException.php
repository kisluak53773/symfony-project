<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

use Exception;

class WrongDataException extends Exception
{
    public function __construct(string $message = 'Default wrong data exception', int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
