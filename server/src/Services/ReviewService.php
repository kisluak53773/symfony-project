<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Entity\Review;
use App\Entity\Product;
use App\Services\Exception\Request\NotFoundException;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ReviewRepository;
use App\Services\Exception\Request\ForbiddenException;
use App\DTO\Review\CreateReviewDto;
use App\DTO\Review\PatchReviewDto;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;

class ReviewService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private PaginatorInterface $paginator,
        private ReviewRepository $reviewRepository
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
        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $product = $this->entityManager->getRepository(Product::class)->find($createReviewDto->productId);

        if (!isset($product)) {
            throw new NotFoundException('Product not found');
        }

        $review = new Review();
        $review->setClient($user);
        $review->setProduct($product);
        $review->setRating($createReviewDto->rating);

        if (isset($createReviewDto->comment)) {
            $review->setComment($createReviewDto->comment);
        }

        $this->entityManager->persist($review);
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
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

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
        $review = $this->entityManager->getRepository(Review::class)->find($id);

        if (!isset($review)) {
            throw new NotFoundException("Such review does not exist");
        }

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        if ($user->getId() !== $review->getClient()->getId()) {
            throw new ForbiddenException('You are not allowd to patch this comment');
        }

        $review->setRating($patchReviewDto->rating);

        if (isset($patchReviewDto->comment)) {
            $review->setComment($patchReviewDto->comment);
        }

        $this->entityManager->persist($review);
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

        $this->entityManager->remove($review);
        $this->entityManager->flush();
    }
}
