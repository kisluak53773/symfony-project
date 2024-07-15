<?php

declare(strict_types=1);

namespace App\Services\Exceptions;

use Exception;

class ServiceException extends Exception
{
    protected $message = 'Default service exception';
}
