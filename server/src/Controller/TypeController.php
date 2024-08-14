<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use App\Services\Exception\Request\RequestException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\DTO\Type\CreatTypeDto;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Contract\Service\TypeServiceInterface;

#[Route('/api/type', name: 'api_type_')]
class TypeController extends AbstractController
{
    public function __construct(private TypeServiceInterface $typeService) {}

    #[Route(name: 'create', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function add(
        #[MapUploadedFile([
            new Assert\File(mimeTypes: ['image/png', 'image/jpeg']),
            new Assert\Image(maxWidth: 3840, maxHeight: 2160),
        ])]
        UploadedFile $image,
        #[MapRequestPayload] CreatTypeDto $creatTypeDto
    ): JsonResponse {
        try {
            $id = $this->typeService->add($image, $creatTypeDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'type created', 'id' => $id], 201);
    }

    #[Route('/vendor', name: 'get_vendor', methods: 'get')]
    public function get(): JsonResponse
    {
        try {
            $types = $this->typeService->get();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $types,
            context: [AbstractNormalizer::GROUPS => ['vendor_type']]
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->typeService->delete($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
