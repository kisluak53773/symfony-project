<?php

declare(strict_types=1);

namespace App\Services\Exception\Access;

use Exception;

class AccessForbiddenException extends Exception
{
    protected $message = 'Default forbidden exception';
}
