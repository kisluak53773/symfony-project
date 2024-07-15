<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Contracts\Strategy;
use App\Services\Contracts\StrategyService;
use App\Services\Exceptions\StrategyBuilderException;

class StrategyBuilder
{
    private static array $config;

    public static function defineStrategy(string $strategyService, string $strategyKey, string $strategy): void
    {
        self::$config[$strategyService][$strategyKey] = $strategy;
    }

    public static function findStrategy(string $strategyService, string $strategyKey): StrategyService
    {
        if (!isset(self::$config[$strategyService]) || !isset(self::$config[$strategyService][$strategyKey])) {
            throw new StrategyBuilderException();
        }

        if (!class_exists($strategyService) || !class_exists(self::$config[$strategyService][$strategyKey])) {
            throw new StrategyBuilderException();
        }

        $serviceInstance = new $strategyService();

        $serviceInstance->addStrategy(new self::$config[$strategyService][$strategyKey], $strategyKey);
        $serviceInstance->setStrategy($strategyKey);

        return $serviceInstance;
    }
}
