<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class TypeNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Type with id $id not found");
    }
}
