<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaginationQueryDto
{
    public function __construct(
        #[Assert\Positive(message: "Page can not be a negative number")]
        public readonly int $limit = 5,

        #[Assert\Positive(message: "Page can not be a negative number")]
        public readonly int $page = 1,
    ) {}
}
