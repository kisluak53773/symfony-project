<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Services\AuthService;
use App\Services\Exception\Request\RequestException;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(private AuthService $authService)
    {
    }

    #[Route('/register', name: 'register', methods: 'post')]
    public function register(): JsonResponse
    {
        try {
            $this->authService->register();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Registered succsessfully'], 201);
    }

    #[Route('/register/vendor', name: 'register_vendor', methods: 'post')]
    public function registerVendor(): JsonResponse
    {
        try {
            $this->authService->registerVendor();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Vendor created'], 201);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->authService->delete($id);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
