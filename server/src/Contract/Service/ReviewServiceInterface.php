<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Review\CreateReviewDto;
use App\DTO\Review\PatchReviewDto;
use App\DTO\PaginationQueryDto;
use App\Entity\Review;

interface ReviewServiceInterface
{
    public function add(CreateReviewDto $createReviewDto): int;


    /**
     * Summary of getByProductId
     * @param int $productId
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: array<Review>
     * }
     */
    public function getByProductId(int $productId, PaginationQueryDto $paginationQueryDto): array;

    public function patchReview(int $id, PatchReviewDto $patchReviewDto): void;

    public function deleteReview(int $id): void;
}
