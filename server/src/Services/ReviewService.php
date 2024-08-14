<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Review;
use App\Entity\Product;
use App\Services\Exception\Request\NotFoundException;
use Knp\Component\Pager\PaginatorInterface;
use App\Services\Exception\Request\ForbiddenException;
use App\DTO\Review\CreateReviewDto;
use App\DTO\Review\PatchReviewDto;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\ReviewRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\ProductRepositoryInterface;
use App\Contract\Service\ReviewServiceInterface;

class ReviewService implements ReviewServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @param \App\Repository\ReviewRepository $reviewRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\ProductRepository $productRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator,
        private ReviewRepositoryInterface $reviewRepository,
        private UserRepositoryInterface $userRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Summary of add
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return int
     */
    public function add(CreateReviewDto $createReviewDto): int
    {
        $user = $this->userRepository->getCurrentUser();
        $product = $this->entityManager->getRepository(Product::class)->find($createReviewDto->productId);

        if (!isset($product)) {
            throw new NotFoundException('Product not found');
        }

        $review = $this->reviewRepository->create($createReviewDto, $user, $product);
        $this->entityManager->flush();

        return $review->getId();
    }

    /**
     * Summary of getByProductId
     * @param int $productId
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return array
     */
    public function getByProductId(int $productId, PaginationQueryDto $paginationQueryDto): array
    {
        $product = $this->productRepository->find($productId);

        if (!isset($product)) {
            throw new NotFoundException('Product not found');
        }

        $querryBuilder = $this->reviewRepository->findReviewsByProduct($product);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $paginationQueryDto->page,
            $paginationQueryDto->limit
        );

        $products = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $products,
        ];

        return $response;
    }

    /**
     * Summary of pathcComment
     * @param int $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * @throws \App\Services\Exception\Request\ForbiddenException
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function patchReview(int $id, PatchReviewDto $patchReviewDto): void
    {
        $review = $this->reviewRepository->find($id);

        if (!isset($review)) {
            throw new NotFoundException("Such review does not exist");
        }

        $user = $this->userRepository->getCurrentUser();

        if ($user->getId() !== $review->getClient()->getId()) {
            throw new ForbiddenException('You are not allowd to patch this comment');
        }

        $this->reviewRepository->patch($patchReviewDto, $review);
        $this->entityManager->flush();
    }

    /**
     * Summary of deleteReview
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function deleteReview(int $id): void
    {
        $review = $this->entityManager->getRepository(Review::class)->find($id);

        if (!isset($review)) {
            throw new NotFoundException('Review not found');
        }

        $this->reviewRepository->remove($review);
        $this->entityManager->flush();
    }
}
