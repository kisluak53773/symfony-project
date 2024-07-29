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
use App\Entity\Vendor;
use DateTimeImmutable;
use App\Services\Validator\UserValidator;
use App\Services\Validator\VendorValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'register', methods: 'post')]
    public function register(
        ManagerRegistry $doctrine,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {
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

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'registered succsessfully'], 201);
    }

    #[Route('/register/vendor', name: 'register_vendor', methods: 'post')]
    public function registerVendor(
        ManagerRegistry $doctrine,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserValidator $userValidator,
        VendorValidator $vendorValidator,
        ValidatorInterface $validator
    ): JsonResponse {
        $entityManager = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        if (!$userValidator->isUserVendorValid($decoded)) {
            return $this->json(['message' => 'insufficient user data provided'], 400);
        }

        if (!$vendorValidator->isVendorValid($decoded)) {
            return $this->json(['message' => 'insufficient vendor data provided'], 400);
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

        $registraionDate = DateTimeImmutable::createFromFormat('Y-m-d', $decoded->registrationDate);
        $registrationCertificateDate = DateTimeImmutable::createFromFormat('Y-m-d', $decoded->registrationCertificateDate);

        $user = new User();
        $hashedPassword = $passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setPhone($phone);
        $user->setAddress($address);
        $user->setEmail($email);
        $user->setFullName($fullName);
        $user->setRoles([RoleConstants::ROLE_VENDOR]);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($user);

        $vendor = new Vendor();
        $vendor->setTitle($decoded->title);
        $vendor->setAddress($decoded->vendorAddress);
        $vendor->setInn($decoded->inn);
        $vendor->setRegistrationAuthority($decoded->registrationAuthority);
        $vendor->setRegistrationDate($registraionDate);
        $vendor->setRegistrationCertificateDate($registrationCertificateDate);
        $vendor->setUser($user);

        $errors = $validator->validate($vendor);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($vendor);

        $entityManager->flush();

        return $this->json(['message' => 'Vendor created'], 201);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    public function delete(int $id, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!isset($user)) {
            return $this->json(['message' => 'no such user exist'], 404);
        }
        $user->setRoles([RoleConstants::ROLE_DELETED]);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
