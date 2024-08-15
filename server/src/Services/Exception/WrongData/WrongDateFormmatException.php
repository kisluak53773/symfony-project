<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class WrongDateFormmatException extends WrongDataException
{
    public function __construct()
    {
        parent::__construct('Wrong date format passed');
    }
}
