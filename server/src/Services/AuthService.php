<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DTO\Auth\RegisterDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\CartRepositoryInterface;
use App\Contract\Service\AuthServiceInterface;
use App\Services\Exception\WrongData\UserAlreadyExistsException;

class AuthService implements AuthServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\CartRepository $cartRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepositoryInterface $userRepository,
        private CartRepositoryInterface $cartRepository,
    ) {}

    /**
     * Summary of register
     * @param \App\DTO\Auth\RegisterDto $registerDto
     * 
     * @throws \App\Services\Exception\WrongData\UserAlreadyExistsException
     * 
     * @return void
     */
    public function register(RegisterDto $registerDto): void
    {
        $userInDb = $this->userRepository->findOneBy(['phone' => $registerDto->phone]);

        if (isset($userInDb)) {
            throw new UserAlreadyExistsException($registerDto->phone);
        }

        $user = $this->userRepository->registerUser($registerDto);
        $this->cartRepository->create($user);

        $this->entityManager->flush();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $this->userRepository->delete($id);

        $this->entityManager->flush();
    }
}
