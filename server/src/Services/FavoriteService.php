<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Product;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\ProductRepositoryInterface;
use App\Contract\Service\FavoriteServiceInterface;
use App\Services\Exception\NotFound\ProductNotFoundException;

class FavoriteService implements FavoriteServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\ProductRepository $productRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepositoryInterface $userRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Summary of addToFavorite
     * @param int $pruductId
     * 
     * @throws \App\Services\Exception\NotFound\ProductNotFoundException
     * 
     * @return void
     */
    public function addToFavorite(int $pruductId): void
    {
        $product = $this->productRepository->find($pruductId);

        if (!isset($product)) {
            throw new ProductNotFoundException($pruductId);
        }

        $this->userRepository->addProductToFavorite($product);
        $this->entityManager->flush();
    }

    /**
     * @return Collection<int, Product>
     */
    public function getFavoriteProducts(): Collection
    {
        $user = $this->userRepository->getCurrentUser();

        return $user->getFavorite();
    }

    /**
     * Summary of deleteFromFavorite
     * @param int $pruductId
     * 
     * @throws \App\Services\Exception\NotFound\ProductNotFoundException
     * 
     * @return void
     */
    public function deleteFromFavorite(int $pruductId): void
    {
        $product = $this->productRepository->find($pruductId);

        if (!isset($product)) {
            throw new ProductNotFoundException($pruductId);
        }

        $this->userRepository->removeProductFromFavorite($product);
        $this->entityManager->flush();
    }
}
