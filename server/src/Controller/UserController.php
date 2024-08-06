<?php

namespace App\Controller;

use App\Enum\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/user', name: 'api_user_')]
class UserController extends AbstractController
{
    #[Route('/current', name: 'get_current', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
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

    #[Route(name: 'patch_current_user', methods: 'patch')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function patchCurrentUser(
        ManagerRegistry $doctrine,
        Request $request,
        Security $security,
        ValidatorInterface $validator
    ): JsonResponse {
        $user = $security->getUser();
        $entityManager = $doctrine->getManager();
        $decoded = json_decode($request->getContent());
        $userPhone = $user->getUserIdentifier();

        if (!isset($decoded->phone)) {
            return $this->json(['message' => 'insufficient data provided'], 400);
        }

        if ($decoded->phone !== $userPhone) {
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['phone' => $decoded->phone]);

            if (isset($existingUser)) {
                return $this->json(['message' => 'user with such phone already exists'], 400);
            }
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $user->setPhone($decoded->phone);

        if (isset($decoded->address)) {
            $user->setAddress($decoded->address);
        }

        if (isset($decoded->email)) {
            $user->setEmail($decoded->email);
        }

        if (isset($decoded->fullName)) {
            $user->setFullName($decoded->fullName);
        }

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'profile updated'], 201);
    }
}
