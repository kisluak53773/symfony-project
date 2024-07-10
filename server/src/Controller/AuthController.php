<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use DateTime;

#[Route('/api/auth', name: 'api_auth')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: 'post')]
    public function index(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        $email = $decoded->email;
        $password = $decoded->password;
        $address = $decoded->address;
        $fullName = $decoded->fullName;

        if (!$email || !$password || !$address || !$fullName) {
            return $this->json(['message' => 'insufficient data provided'], 400);
        }

        $userInDb = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (isset($userInDb)) {
            return $this->json(['message' => 'email is already taken'], 400);
        }

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setAddress($address);
        $user->setFullName($fullName);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'registered succsessfully'], 201);
    }
}
