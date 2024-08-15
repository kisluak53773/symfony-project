<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Review;
use App\Entity\Product;
use App\DTO\Review\CreateReviewDto;
use App\DTO\Review\PatchReviewDto;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\ReviewRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\ProductRepositoryInterface;
use App\Contract\Service\ReviewServiceInterface;
use App\Contract\PaginationHandlerInterface;
use App\Services\Exception\NotFound\ProductNotFoundException;
use App\Services\Exception\NotFound\ReviewNotFoundException;
use App\Services\Exception\Access\NotAllowedToPatchReviewException;

class ReviewService implements ReviewServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Contract\PaginationHandlerInterface<Review> $paginationHandler
     * @param \App\Repository\ReviewRepository $reviewRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\ProductRepository $productRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginationHandlerInterface $paginationHandler,
        private ReviewRepositoryInterface $reviewRepository,
        private UserRepositoryInterface $userRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Summary of add
     * @param \App\DTO\Review\CreateReviewDto $createReviewDto
     * 
     * @throws \App\Services\Exception\NotFound\ProductNotFoundException
     * 
     * @return int
     */
    public function add(CreateReviewDto $createReviewDto): int
    {
        $user = $this->userRepository->getCurrentUser();
        $product = $this->entityManager->getRepository(Product::class)->find($createReviewDto->productId);

        if (!isset($product)) {
            throw new ProductNotFoundException($createReviewDto->productId);
        }

        $review = $this->reviewRepository->create($createReviewDto, $user, $product);
        $this->entityManager->flush();

        return $review->getId() ?? 0;
    }

    /**
     * Summary of getByProductId
     * @param int $productId
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @throws \App\Services\Exception\NotFound\ProductNotFoundException
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function getByProductId(int $productId, PaginationQueryDto $paginationQueryDto): array
    {
        $product = $this->productRepository->find($productId);

        if (!isset($product)) {
            throw new ProductNotFoundException($productId);
        }

        $querryBuilder = $this->reviewRepository->findReviewsByProduct($product);
        $response = $this->paginationHandler->handlePagination($querryBuilder, $paginationQueryDto);

        return $response;
    }

    /**
     * Summary of patchReview
     * @param int $id
     * @param \App\DTO\Review\PatchReviewDto $patchReviewDto
     * 
     * @throws \App\Services\Exception\NotFound\ReviewNotFoundException
     * @throws \App\Services\Exception\Access\NotAllowedToPatchReviewException
     * 
     * @return void
     */
    public function patchReview(int $id, PatchReviewDto $patchReviewDto): void
    {
        $review = $this->reviewRepository->find($id);

        if (!isset($review)) {
            throw new ReviewNotFoundException($id);
        }

        $user = $this->userRepository->getCurrentUser();

        if ($review->getClient() && $user->getId() !== $review->getClient()->getId()) {
            throw new NotAllowedToPatchReviewException();
        }

        $this->reviewRepository->patch($patchReviewDto, $review);
        $this->entityManager->flush();
    }

    /**
     * Summary of deleteReview
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\ReviewNotFoundException
     * 
     * @return void
     */
    public function deleteReview(int $id): void
    {
        $review = $this->entityManager->getRepository(Review::class)->find($id);

        if (!isset($review)) {
            throw new ReviewNotFoundException($id);
        }

        $this->reviewRepository->remove($review);
        $this->entityManager->flush();
    }
}
