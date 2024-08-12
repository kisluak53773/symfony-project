<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Product;
use App\Entity\User;
use App\Services\Exception\Request\NotFoundException;
use Doctrine\Common\Collections\Collection;

class FavoriteService
{
    public function __construct(
        private ManagerRegistry $registry,
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
        $entityManager = $this->registry->getManager();
        $product = $entityManager->getRepository(Product::class)->find($pruductId);

        if (!isset($product)) {
            throw new NotFoundException();
        }

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $user->addFavorite($product);

        $entityManager->persist($user);
        $entityManager->flush();
    }

    /**
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return Collection<int, Product>
     */
    public function getFavoriteProducts(): Collection
    {
        $entityManager = $this->registry->getManager();
        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

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
        $entityManager = $this->registry->getManager();
        $product = $entityManager->getRepository(Product::class)->find($pruductId);

        if (!isset($product)) {
            throw new NotFoundException();
        }

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $user->removeFavorite($product);

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
