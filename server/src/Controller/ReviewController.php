<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Services\ReviewService;
use App\Services\Exception\Request\RequestException;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('/api/review', name: 'api_review_')]
class ReviewController extends AbstractController
{
    public function __construct(private ReviewService $reviewService) {}

    #[Route(name: 'add_review', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function add(Request $request): JsonResponse
    {
        try {
            $id = $this->reviewService->add($request);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'New review craeted', 'id' => $id], 201);
    }

    #[Route('/product/{productId<\d+>}', name: 'get_reviews_by_product', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getByProductId(int $productId, Request $request): JsonResponse
    {
        try {
            $response = $this->reviewService->getByProductId($productId, $request);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['product_reviews']]
        );
    }

    #[Route('/{id<\d+>}', name: 'patch_review', methods: 'patch')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function pathcReview(int $id, Request $request): JsonResponse
    {
        try {
            $this->reviewService->patchReview($id, $request);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Review patched'], 200);
    }

    #[Route('/{id<\d+>}', name: 'delete_review', methods: 'delete')]
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
