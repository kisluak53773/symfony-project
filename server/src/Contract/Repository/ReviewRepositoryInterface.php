<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use App\DTO\Review\CreateReviewDto;
use App\Entity\User;
use App\DTO\Review\PatchReviewDto;
use App\Entity\Review;

interface ReviewRepositoryInterface
{
    public function findReviewsByProduct(Product $product): QueryBuilder;

    public function create(CreateReviewDto $createReviewDto, User $user, Product $product): Review;

    public function patch(PatchReviewDto $patchReviewDto, Review $review): void;

    public function remove(Review $review): void;
}
