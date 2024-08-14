<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\DTO\Review\CreateReviewDto;
use App\DTO\Review\PatchReviewDto;
use App\DTO\PaginationQueryDto;
use App\Contract\Service\ReviewServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;


#[Route('/api/review', name: 'api_review_')]
class ReviewController extends AbstractController
{
    public function __construct(private ReviewServiceInterface $reviewService) {}

    #[Route(name: 'add_review', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function add(#[MapRequestPayload] CreateReviewDto $createReviewDto): JsonResponse
    {
        try {
            $id = $this->reviewService->add($createReviewDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'New review craeted', 'id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/product/{productId}', name: 'get_reviews_by_product', methods: 'get', requirements: ['productId' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getByProductId(
        int $productId,
        #[MapQueryString] PaginationQueryDto $paginationQueryDto = new PaginationQueryDto()
    ): JsonResponse {
        try {
            $response = $this->reviewService->getByProductId($productId, $paginationQueryDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
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
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Review patched'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete_review', methods: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function deleteReview(int $id): JsonResponse
    {
        try {
            $this->reviewService->deleteReview($id);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Review deleted'], Response::HTTP_OK);
    }
}
