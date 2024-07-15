<?php

namespace App\Services;

use App\Services\StrategyBuilder;
use App\Services\CommandLineService;
use App\Services\DataStrategy;
use App\Services\ProductStrategy;

StrategyBuilder::defineStrategy(CommandLineService::class, 'data', DataStrategy::class);
StrategyBuilder::defineStrategy(CommandLineService::class, 'product', ProductStrategy::class);
