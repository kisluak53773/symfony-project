<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface CommandLineStrategy extends Strategy
{
    public function generate(): string;
}
