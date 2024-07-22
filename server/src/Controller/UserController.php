<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/user', name: 'api_user_')]
class UserController extends AbstractController
{
    #[Route('/current', name: 'get_current', methods: 'get')]
    public function index(Security $security): JsonResponse
    {
        $user = $security->getUser();

        if (!isset($user)) {
            return $this->json(['message' => 'user not found'], 404);
        }

        return $this->json(
            data: $user,
            context: [AbstractNormalizer::GROUPS => ['current_user']]
        );
    }
}
