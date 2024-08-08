<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Enum\Role;
use App\Entity\User;
use App\Entity\Cart;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\Entity\Vendor;
use App\Services\Validator\VendorValidator;
use App\Services\Validator\UserValidator;
use DateTimeImmutable;

class AuthService
{
    public function __construct(
        private  ManagerRegistry $registry,
        private  UserPasswordHasherInterface $passwordHasher,
        private  ValidatorInterface $validator,
        private VendorValidator $vendorValidator,
        private  UserValidator $userValidator,
    ) {
    }

    /**
     * Summary of register
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function register(Request $request): void
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->password) || !isset($decoded->phone)) {
            throw new BadRequsetException();
        }

        $phone = $decoded->phone;
        $password = $decoded->password;

        $userInDb = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (isset($userInDb)) {
            throw new BadRequsetException('User with such phone already exists');
        }

        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setPhone($phone);
        $user->setRoles([Role::ROLE_USER->value]);

        if (isset($decoded->address)) {
            $user->setAddress($decoded->address);
        }

        if (isset($decoded->email)) {
            $user->setEmail($decoded->email);
        }

        if (isset($decoded->fullName)) {
            $user->setFullName($decoded->fullName);
        }

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($user);

        $cart = new Cart();
        $cart->setCustomer($user);

        $errors = $this->validator->validate($cart);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($cart);

        $entityManager->flush();
    }

    /**
     * Summary of registerVendor
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function registerVendor(Request $request): void
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($request->getContent());

        $this->userValidator->isUserVendorValid($decoded);
        $this->vendorValidator->isVendorValid($decoded);

        $phone = $decoded->phone;
        $password = $decoded->password;
        $address = $decoded->address;
        $email = $decoded->email;
        $fullName = $decoded->fullName;

        $userInDb = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (isset($userInDb)) {
            throw new BadRequsetException('User with such phone already exists');
        }

        $registraionDate = DateTimeImmutable::createFromFormat('Y-m-d', $decoded->registrationDate);
        $registrationCertificateDate = DateTimeImmutable::createFromFormat('Y-m-d', $decoded->registrationCertificateDate);

        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setPhone($phone);
        $user->setAddress($address);
        $user->setEmail($email);
        $user->setFullName($fullName);
        $user->setRoles([Role::ROLE_VENDOR->value]);

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
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

        $errors = $this->validator->validate($vendor);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($vendor);

        $entityManager->flush();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $entityManager = $this->registry->getManager();

        $user = $entityManager->getRepository(User::class)->find($id);

        if (!isset($user)) {
            throw new NotFoundException();
        }

        $user->setRoles([Role::ROLE_DELETED->value]);

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
