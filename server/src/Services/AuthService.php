<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Enum\Role;
use App\Entity\User;
use App\Entity\Cart;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\Entity\Vendor;
use DateTimeImmutable;
use App\DTO\Auth\RegisterDto;
use App\DTO\Auth\RegisterVendorDto;

class AuthService
{
    public function __construct(
        private  ManagerRegistry $registry,
        private  UserPasswordHasherInterface $passwordHasher,
    ) {}

    /**
     * Summary of register
     * @param \App\DTO\Auth\RegisterDto $registerDto
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function register(RegisterDto $registerDto): void
    {
        $entityManager = $this->registry->getManager();

        $phone = $registerDto->phone;
        $password = $registerDto->password;

        $userInDb = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (isset($userInDb)) {
            throw new BadRequsetException('User with such phone already exists');
        }

        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setPhone($phone);
        $user->setRoles([Role::ROLE_USER->value]);

        if (isset($registerDto->address)) {
            $user->setAddress($registerDto->address);
        }

        if (isset($registerDto->email)) {
            $user->setEmail($registerDto->email);
        }

        if (isset($decoded->fullName)) {
            $user->setFullName($registerDto->fullName);
        }

        $entityManager->persist($user);

        $cart = new Cart();
        $cart->setCustomer($user);
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
    public function registerVendor(RegisterVendorDto $registerVendorDto): void
    {
        $entityManager = $this->registry->getManager();

        $phone = $registerVendorDto->phone;
        $password = $registerVendorDto->password;
        $address = $registerVendorDto->address;
        $email = $registerVendorDto->email;
        $fullName = $registerVendorDto->fullName;

        $userInDb = $entityManager->getRepository(User::class)->findOneBy(['phone' => $phone]);

        if (isset($userInDb)) {
            throw new BadRequsetException('User with such phone already exists');
        }

        $registraionDate = DateTimeImmutable::createFromFormat('Y-m-d', $registerVendorDto->registrationDate);
        $registrationCertificateDate = DateTimeImmutable::createFromFormat('Y-m-d', $registerVendorDto->registrationCertificateDate);

        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setPhone($phone);
        $user->setAddress($address);
        $user->setEmail($email);
        $user->setFullName($fullName);
        $user->setRoles([Role::ROLE_VENDOR->value]);
        $entityManager->persist($user);

        $cart = new Cart();
        $cart->setCustomer($user);
        $entityManager->persist($cart);

        $vendor = new Vendor();
        $vendor->setTitle($registerVendorDto->title);
        $vendor->setAddress($registerVendorDto->vendorAddress);
        $vendor->setInn($registerVendorDto->inn);
        $vendor->setRegistrationAuthority($registerVendorDto->registrationAuthority);
        $vendor->setRegistrationDate($registraionDate);
        $vendor->setRegistrationCertificateDate($registrationCertificateDate);
        $vendor->setUser($user);
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
