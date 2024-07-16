<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: 'post')]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->password) || !isset($decoded->phone)) {
            return $this->json(['message' => 'insufficient data provided'], 400);
        }

        $phone = $decoded->phone;
        $password = $decoded->password;

        $userInDb = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (isset($userInDb)) {
            return $this->json(['message' => 'youy already have an account'], 400);
        }

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setPhone($phone);

        if (isset($decoded->address)) {
            $user->setAddress($decoded->address);
        }

        if (isset($decoded->email)) {
            $user->setEmail($decoded->email);
        }

        if (isset($decoded->fullName)) {
            $user->setFullName($decoded->fullName);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'registered succsessfully'], 201);
    }
}
