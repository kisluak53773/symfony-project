<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface StrategyService
{
    public function addStrategy(Strategy $strategy, string $name): self;

    public function setStrategy(string $strategy): void;

    public function resolve(): string;
}
