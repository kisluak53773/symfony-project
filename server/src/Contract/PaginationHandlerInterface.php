<?php

declare(strict_types=1);

namespace App\Contract;

use App\DTO\PaginationQueryDto;

interface PaginationHandlerInterface
{
    /**
     * @param mixed $queryBuilder
     * @param PaginationQueryDto $paginationQueryDto
     * 
     * @return array{total_items: int, current_page: int, total_pages: int, data: array}
     */
    public function handlePagination(mixed $querryBuilder, PaginationQueryDto $paginationQueryDto): array;
}
