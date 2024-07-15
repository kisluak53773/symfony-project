<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\CommandLineStrategy;

class DataStrategy implements CommandLineStrategy
{
    public function generate(): string
    {
        return 'Alex';
    }
}
