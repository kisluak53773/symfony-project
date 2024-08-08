<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\BadRequsetException;

class UserService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Security $security,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Summary of index
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return \Symfony\Component\Security\Core\User\UserInterface
     */
    public function index(): UserInterface
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
    public function patchCurrentUser(Request $request): void
    {
        $user = $this->security->getUser();
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($request->getContent());
        $userPhone = $user->getUserIdentifier();

        if (!isset($decoded->phone)) {
            throw new BadRequsetException();
        }

        if ($decoded->phone !== $userPhone) {
            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['phone' => $decoded->phone]);

            if (isset($existingUser)) {
                throw new BadRequsetException('User with such phone already exists');
            }
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $user->setPhone($decoded->phone);

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
        $entityManager->flush();
    }
}
