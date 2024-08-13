<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Services\Exception\Request\BadRequsetException;
use App\DTO\User\PatchUserDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\UserRepositoryInterface;

class UserService
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
     * Summary of index
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getCurrentUser(): UserInterface
    {
        return $this->userRepository->getCurrentUser();
    }

    /**
     * Summary of patchCurrentUser
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
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
                throw new BadRequsetException('User with such phone already exists');
            }
        }

        $this->userRepository->patch($patchUserDto, $user);
        $this->entityManager->flush();
    }
}
