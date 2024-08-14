<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Review\CreateReviewDto;
use App\DTO\Review\PatchReviewDto;
use App\DTO\PaginationQueryDto;

interface ReviewServiceInterface
{
    public function add(CreateReviewDto $createReviewDto): int;

    public function getByProductId(int $productId, PaginationQueryDto $paginationQueryDto): array;

    public function patchReview(int $id, PatchReviewDto $patchReviewDto): void;

    public function deleteReview(int $id): void;
}
