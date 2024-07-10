<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\Strategy;
use App\Services\Contracts\StrategyService;

class CommandLineService implements StrategyService
{
    private string $strategy;
    private array $strategies;

    public function addStrategy(Strategy $strategy, string $name): self
    {
        $this->strategies[$name] = $strategy;

        return $this;
    }

    public function setStrategy(string $strategy): void
    {
        $this->strategy = $strategy;
    }

    public function resolve(): string
    {
        if (isset($this->strategies[$this->strategy])) {
            return $this->strategies[$this->strategy]->generate();
        }

        return '';
    }
}
