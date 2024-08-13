<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\DTO\Producer\CreateProducerDto;
use App\Entity\Producer;

interface ProducerRepositoryInterface
{
    public function create(CreateProducerDto $createProducerDto): Producer;

    public function remove(Producer $producer): void;
}
