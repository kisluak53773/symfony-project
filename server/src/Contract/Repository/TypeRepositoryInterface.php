<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\DTO\Type\CreatTypeDto;
use App\Entity\Type;

interface TypeRepositoryInterface
{
    public function create(CreatTypeDto $creatTypeDto, string $imagePath): Type;

    public function remove(Type $type): void;
}
