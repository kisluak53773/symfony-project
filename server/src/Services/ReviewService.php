<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Services\Validator\ReviewValidator;
use App\Entity\User;
use App\Entity\Review;
use App\Entity\Product;
use App\Services\Exception\Request\NotFoundException;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ReviewRepository;

class ReviewService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Security $security,
        private ReviewValidator $reviewValidator,
        private ValidatorInterface $validator,
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
    public function add(Request $request): int
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($request->getContent());

        $this->reviewValidator->isReviewValid($decoded);

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $product = $entityManager->getRepository(Product::class)->find($decoded->productId);

        if (!isset($product)) {
            throw new NotFoundException('Product not found');
        }

        $review = new Review();
        $review->setClient($user);
        $review->setProduct($product);
        $review->setRating($decoded->rating);

        if (isset($decoded->comment)) {
            $review->setComment($decoded->comment);
        }

        $entityManager->persist($review);
        $entityManager->flush();

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
    public function getByProductId(int $productId, Request $request): array
    {
        $entityManager = $this->registry->getManager();
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!isset($product)) {
            throw new NotFoundException('Product not found');
        }

        $querryBuilder = $this->reviewRepository->findReviewsByProduct($product);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
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
     * Summary of deleteReview
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function deleteReview(int $id): void
    {
        $entityManager = $this->registry->getManager();
        $review = $entityManager->getRepository(Review::class)->find($id);

        if (!isset($review)) {
            throw new NotFoundException('Review not found');
        }

        $entityManager->remove($review);
        $entityManager->flush();
    }
}
