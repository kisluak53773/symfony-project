<?php

declare(strict_types=1);

namespace App\Services\Exception\Request;

use Exception;

class RequestException extends Exception
{
    protected $message = 'Default service exception';

    protected $statusCode = 500;

    public function getStatsCode(): int
    {
        return $this->statusCode;
    }
}
