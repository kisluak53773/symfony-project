<?php

declare(strict_types=1);

namespace App\Contract;

use App\DTO\PaginationQueryDto;

/**
 * @template T
 */
interface PaginationHandlerInterface
{
    /**
     * @param mixed $queryBuilder
     * @param PaginationQueryDto $paginationQueryDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function handlePagination(mixed $queryBuilder, PaginationQueryDto $paginationQueryDto): array;
}
