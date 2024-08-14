<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Producer\CreateProducerDto;

interface ProducerServiceInterface
{
    public function add(CreateProducerDto $createProducerDto): int;

    public function getForVendor(): array;

    public function delete(int $id): void;
}
