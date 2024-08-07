<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use App\Services\ProducerService;
use App\Services\Exception\Request\RequestException;

#[Route('/api/producer', name: 'api_producer_')]
class ProducerController extends AbstractController
{
    public function __construct(private ProducerService $producerService)
    {
    }

    #[Route(name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function add(): JsonResponse
    {
        try {
            $id = $this->producerService->add();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'producer created', 'id' => $id], 201);
    }

    #[Route('/vendor', name: 'get_vendor', methods: 'get')]
    public function getForVendor(): JsonResponse
    {
        try {
            $producers = $this->producerService->getForVendor();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $producers,
            context: [AbstractNormalizer::GROUPS => ['vendor_producer']]
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->producerService->delete($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
