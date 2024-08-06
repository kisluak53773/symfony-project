<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Product;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Enum\Role;

#[Route('/api/favorite', name: 'api_favorite_')]
class FavoriteController extends AbstractController
{
    #[Route('/{pruductId<\d+>}', name: 'add_prodct_to_favorite', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function addToFavorite(
        ManagerRegistry $registry,
        int $pruductId,
        Security $security
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $product = $entityManager->getRepository(Product::class)->find($pruductId);

        if (!isset($product)) {
            return $this->json(['message' => 'Produt is not found'], 404);
        }

        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $user->addFavorite($product);
        $product->addUser($user);

        $entityManager->persist($user);
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['message' => 'Product added to favorite'], 200);
    }

    #[Route(name: 'add', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getFavoriteProducts(ManagerRegistry $registry, Security $security): JsonResponse
    {
        $entityManager = $registry->getManager();
        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $favoriteProducts = $user->getFavorite();

        return $this->json(
            data: $favoriteProducts,
            context: [AbstractNormalizer::GROUPS => ['favorite_products']]
        );
    }

    #[Route('/{pruductId<\d+>}', name: 'delete_prodct_from_favorite', methods: 'delete')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function deleteFromFavorite(
        ManagerRegistry $registry,
        int $pruductId,
        Security $security
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $product = $entityManager->getRepository(Product::class)->find($pruductId);

        if (!isset($product)) {
            return $this->json(['message' => 'Produt is not found'], 404);
        }

        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $user->removeFavorite($product);
        $product->removeUser($user);

        $entityManager->persist($user);
        $entityManager->persist($product);
        $entityManager->flush();

        return $this->json(['message' => 'Product added to favorite'], 200);
    }
}
