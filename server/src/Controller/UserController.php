<?php

namespace App\Controller;

use App\Enum\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Services\UserService;
use App\Services\Exception\Request\RequestException;

#[Route('/api/user', name: 'api_user_')]
class UserController extends AbstractController
{
    public function __construct(private UserService $userService)
    {
    }

    #[Route('/current', name: 'get_current', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function index(): JsonResponse
    {
        try {
            $user = $this->userService->index();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $user,
            context: [AbstractNormalizer::GROUPS => ['current_user']]
        );
    }

    #[Route(name: 'patch_current_user', methods: 'patch')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function patchCurrentUser(): JsonResponse
    {
        try {
            $this->userService->patchCurrentUser();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'profile updated'], 201);
    }
}
