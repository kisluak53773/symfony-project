<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\ReviewService;
use App\Services\Exception\Request\RequestException;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\DTO\Review\CreateReviewDto;
use App\DTO\Review\PatchReviewDto;
use App\DTO\PaginationQueryDto;


#[Route('/api/review', name: 'api_review_')]
class ReviewController extends AbstractController
{
    public function __construct(private ReviewService $reviewService) {}

    #[Route(name: 'add_review', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function add(#[MapRequestPayload] CreateReviewDto $createReviewDto): JsonResponse
    {
        try {
            $id = $this->reviewService->add($createReviewDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'New review craeted', 'id' => $id], 201);
    }

    #[Route('/product/{productId}', name: 'get_reviews_by_product', methods: 'get', requirements: ['productId' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getByProductId(
        int $productId,
        #[MapQueryString] PaginationQueryDto $paginationQueryDto
    ): JsonResponse {
        try {
            $response = $this->reviewService->getByProductId($productId, $paginationQueryDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['product_reviews']]
        );
    }

    #[Route('/{id}', name: 'patch_review', methods: 'patch', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function pathcReview(int $id, #[MapRequestPayload] PatchReviewDto $patchReviewDto): JsonResponse
    {
        try {
            $this->reviewService->patchReview($id, $patchReviewDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Review patched'], 200);
    }

    #[Route('/{id}', name: 'delete_review', methods: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function deleteReview(int $id): JsonResponse
    {
        try {
            $this->reviewService->deleteReview($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Review deleted'], 200);
    }
}
