<?php

declare(strict_types=1);

namespace App\Contract;

use App\DTO\PaginationQueryDto;

interface PaginationHandlerInterface
{
    public function handlePagination(mixed $querryBuilder, PaginationQueryDto $paginationQueryDto): array;
}
