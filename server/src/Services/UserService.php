<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Security\Core\User\UserInterface;
use App\DTO\User\PatchUserDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Service\UserServiceInterface;
use App\Services\Exception\WrongData\UserAlreadyExistsException;

class UserService implements UserServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\UserRepository $userRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepositoryInterface $userRepository,
    ) {}

    /**
     * Summary of getCurrentUser
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getCurrentUser(): UserInterface
    {
        return $this->userRepository->getCurrentUser();
    }

    /**
     * Summary of patchCurrentUser
     * @param \App\DTO\User\PatchUserDto $patchUserDto
     * 
     * @throws \App\Services\Exception\WrongData\UserAlreadyExistsException
     * 
     * @return void
     */
    public function patchCurrentUser(PatchUserDto $patchUserDto): void
    {
        $user = $this->userRepository->getCurrentUser();
        $userPhone = $user->getUserIdentifier();

        if ($patchUserDto->phone !== $userPhone) {
            $existingUser = $this->userRepository->findOneBy(['phone' => $patchUserDto->phone]);

            if (isset($existingUser)) {
                throw new UserAlreadyExistsException($patchUserDto->phone);
            }
        }

        $this->userRepository->patch($patchUserDto, $user);
        $this->entityManager->flush();
    }
}
