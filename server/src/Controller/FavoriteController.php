<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Enum\Role;
use App\Services\Exception\Request\RequestException;
use App\Services\FavoriteService;

#[Route('/api/favorite', name: 'api_favorite_')]
class FavoriteController extends AbstractController
{
    public function __construct(private FavoriteService $favoriteService)
    {
    }

    #[Route('/{productId<\d+>}', name: 'add_prodct_to_favorite', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function addToFavorite(int $productId): JsonResponse
    {
        try {
            $this->favoriteService->addToFavorite($productId);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Product added to favorite'], 200);
    }

    #[Route(name: 'add', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getFavoriteProducts(FavoriteService $favoriteService): JsonResponse
    {
        try {
            $favoriteProducts = $this->favoriteService->getFavoriteProducts();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $favoriteProducts,
            context: [AbstractNormalizer::GROUPS => ['favorite_products']]
        );
    }

    #[Route('/{productId<\d+>}', name: 'delete_prodct_from_favorite', methods: 'delete')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function deleteFromFavorite(int $productId, FavoriteService $favoriteService): JsonResponse
    {
        try {
            $this->favoriteService->deleteFromFavorite($productId);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Product added to favorite'], 200);
    }
}
