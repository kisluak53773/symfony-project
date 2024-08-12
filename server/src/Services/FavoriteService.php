<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Product;
use App\Entity\User;
use App\Services\Exception\Request\NotFoundException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class FavoriteService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {}

    /**
     * Summary of addToFavorite
     * @param int $pruductId
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function addToFavorite(int $pruductId): void
    {
        $product = $this->entityManager->getRepository(Product::class)->find($pruductId);

        if (!isset($product)) {
            throw new NotFoundException();
        }

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $user->addFavorite($product);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return Collection<int, Product>
     */
    public function getFavoriteProducts(): Collection
    {
        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        if (!isset($user)) {
            throw new NotFoundException();
        }

        return $user->getFavorite();
    }

    /**
     * Summary of deleteFromFavorite
     * @param int $pruductId
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function deleteFromFavorite(int $pruductId): void
    {
        $product = $this->entityManager->getRepository(Product::class)->find($pruductId);

        if (!isset($product)) {
            throw new NotFoundException();
        }

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $user->removeFavorite($product);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
