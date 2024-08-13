<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

use Exception;

class NotFoundException extends Exception
{
    protected $message = 'Default not found exception';
}
