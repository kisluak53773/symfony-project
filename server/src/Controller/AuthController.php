<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\User;
use App\Constants\RoleConstants;
use App\Services\User\UserValidator;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: 'post')]
    public function register(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
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
        $user->setRoles([RoleConstants::ROLE_USER]);

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

    public function registerVendor(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher, UserValidator $validator): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        if (!$validator->isVendorValid($decoded)) {
            return $this->json(['message' => 'insufficient data provided'], 400);
        }

        $phone = $decoded->phone;
        $password = $decoded->password;
        $address = $decoded->address;
        $email = $decoded->email;
        $fullName = $decoded->fullName;

        $userInDb = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (isset($userInDb)) {
            return $this->json(['message' => 'youy already have an account'], 400);
        }

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setPhone($phone);
        $user->setAddress($address);
        $user->setEmail($email);
        $user->setFullName($fullName);
        $user->setRoles([RoleConstants::ROLE_VENDOR]);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'Vendor created'], 201);
    }
}
