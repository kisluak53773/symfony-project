<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use App\DTO\Review\CreateReviewDto;
use App\Entity\User;
use App\DTO\Review\PatchReviewDto;
use App\Contract\Repository\ReviewRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository implements ReviewRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * Summary of findReviewsByProduct
     * @param \App\Entity\Product $product
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findReviewsByProduct(Product $product): QueryBuilder
    {
        return $this->createQueryBuilder('r')
            ->where('r.product = :product')
            ->setParameter('product', $product)
            ->orderBy('r.createdAt', 'DESC');
    }

    /**
     * Summary of create
     * @param \App\DTO\Review\CreateReviewDto $createReviewDto
     * @param \App\Entity\User $user
     * @param \App\Entity\Product $product
     * 
     * @return \App\Entity\Review
     */
    public function create(CreateReviewDto $createReviewDto, User $user, Product $product): Review
    {
        $review = new Review();
        $review->setClient($user);
        $review->setProduct($product);
        $review->setRating($createReviewDto->rating);

        if (isset($createReviewDto->comment)) {
            $review->setComment($createReviewDto->comment);
        }

        $this->getEntityManager()->persist($review);

        return $review;
    }

    /**
     * Summary of patch
     * @param \App\DTO\Review\PatchReviewDto $patchReviewDto
     * @param \App\Entity\Review $review
     * 
     * @return void
     */
    public function patch(PatchReviewDto $patchReviewDto, Review $review): void
    {
        $review->setRating($patchReviewDto->rating);

        if (isset($patchReviewDto->comment)) {
            $review->setComment($patchReviewDto->comment);
        }

        $this->getEntityManager()->persist($review);
    }

    /**
     * Summary of remove
     * @param \App\Entity\Review $review
     * 
     * @return void
     */
    public function remove(Review $review): void
    {
        $this->getEntityManager()->remove($review);
    }
}
