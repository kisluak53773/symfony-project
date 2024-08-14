<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Enum\Role;
use App\Contract\Service\FavoriteServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

#[Route('/api/favorite', name: 'api_favorite_')]
class FavoriteController extends AbstractController
{
    public function __construct(private FavoriteServiceInterface $favoriteService) {}

    #[Route('/{productId}', name: 'add_prodct_to_favorite', methods: 'post', requirements: ['productId' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function addToFavorite(int $productId): JsonResponse
    {
        try {
            $this->favoriteService->addToFavorite($productId);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Product added to favorite'], Response::HTTP_OK);
    }

    #[Route(name: 'add', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getFavoriteProducts(): JsonResponse
    {
        try {
            $favoriteProducts = $this->favoriteService->getFavoriteProducts();
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(
            data: $favoriteProducts,
            context: [AbstractNormalizer::GROUPS => ['product_list']]
        );
    }

    #[Route('/{productId}', name: 'delete_prodct_from_favorite', methods: 'delete', requirements: ['productId' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function deleteFromFavorite(int $productId): JsonResponse
    {
        try {
            $this->favoriteService->deleteFromFavorite($productId);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Product added to favorite'], Response::HTTP_OK);
    }
}
