<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Producer\CreateProducerDto;
use App\Entity\Producer;

interface ProducerServiceInterface
{
    public function add(CreateProducerDto $createProducerDto): int;

    /**
     * Summary of getForVendor
     * @return Producer[]
     */
    public function getForVendor(): array;

    public function delete(int $id): void;
}
