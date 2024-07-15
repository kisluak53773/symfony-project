<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\StrategyService;

class StrategyContainer
{
    private array $strategies = [];

    public function __construct(CommandLineService $commandLineService, DataStrategy $dataStrategy, ProductStrategy $productStrategy)
    {
        $commandLineService->addStrategy($dataStrategy, 'data')
            ->addStrategy($productStrategy, 'product');

        $this->strategies['cli'] = $commandLineService;
    }

    public function getStrategy(string $strategy): StrategyService
    {
        if (!isset($this->strategies[$strategy])) {
            throw new \InvalidArgumentException(sprintf('', $strategy));
        }

        return $this->strategies[$strategy];
    }
}
