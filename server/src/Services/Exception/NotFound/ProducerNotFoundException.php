<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class ProducerNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Producer with id $id not found");
    }
}
