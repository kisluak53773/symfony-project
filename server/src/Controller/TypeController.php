<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use App\Services\TypeService;
use App\Services\Exception\Request\RequestException;

#[Route('/api/type', name: 'api_type_')]
class TypeController extends AbstractController
{
    public function __construct(private TypeService $typeService)
    {
    }

    #[Route(name: 'create', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function add(): JsonResponse
    {
        try {
            $id = $this->typeService->add();
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

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
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
