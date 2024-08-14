<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

use Exception;

class WrongDataException extends Exception
{
    protected $message = 'Default wrong data exception';
}
