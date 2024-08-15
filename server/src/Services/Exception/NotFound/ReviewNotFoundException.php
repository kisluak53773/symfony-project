<?php

declare(strict_types=1);

namespace App\Services\Exception\NotFound;

class ReviewNotFoundException extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("Review with id $id not found");
    }
}
