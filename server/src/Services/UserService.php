<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\BadRequsetException;
use App\DTO\User\PatchUserDto;

class UserService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Security $security,
    ) {}

    /**
     * Summary of index
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function getCurrentUser(): UserInterface
    {
        $user = $this->security->getUser();

        if (!isset($user)) {
            throw new NotFoundException('User not found');
        }

        return $user;
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
        $user = $this->security->getUser();
        $entityManager = $this->registry->getManager();
        $userPhone = $user->getUserIdentifier();

        if ($patchUserDto->phone !== $userPhone) {
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['phone' => $patchUserDto->phone]);

            if (isset($existingUser)) {
                throw new BadRequsetException('User with such phone already exists');
            }
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $user->setPhone($patchUserDto->phone);

        if (isset($patchUserDto->address)) {
            $user->setAddress($patchUserDto->address);
        }

        if (isset($patchUserDto->email)) {
            $user->setEmail($patchUserDto->email);
        }

        if (isset($patchUserDto->fullName)) {
            $user->setFullName($patchUserDto->fullName);
        }

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
