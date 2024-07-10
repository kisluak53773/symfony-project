<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\CommandLineStrategy;

class ProductStrategy implements CommandLineStrategy
{
    public function generate(): string
    {
        return 'Jhon';
    }
}
