<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use App\DTO\Auth\RegisterDto;
use App\Entity\Product;
use App\Enum\Role;
use App\DTO\User\PatchUserDto;
use App\Contract\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Services\Exception\NotFound\UserNotFoundException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Services\Exception\Access\NotAtuheticatedException;
use App\Services\Exception\NotFound\UserNotFoundByIdentifierException;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    /**
     * Summary of security
     * @var \Symfony\Bundle\SecurityBundle\Security
     */
    private AuthorizationCheckerInterface $security;
    private UserPasswordHasherInterface $passwordHasher;

    /**
     * Summary of __construct
     * @param \Doctrine\Persistence\ManagerRegistry $registry
     * @param \Symfony\Bundle\SecurityBundle\Security $security
     * @param \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(
        ManagerRegistry $registry,
        AuthorizationCheckerInterface $security,
        UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct($registry, User::class);

        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Summary of upgradePassword
     * @param \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface $user
     * @param string $newHashedPassword
     * 
     * @throws \Symfony\Component\Security\Core\Exception\UnsupportedUserException
     * 
     * @return void
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Summary of registerUser
     * @param \App\DTO\Auth\RegisterDto $registerDto
     * 
     * @return \App\Entity\User
     */
    public function registerUser(RegisterDto $registerDto): User
    {
        $user = new User();
        $hashedPassword = $this->passwordHasher->hashPassword($user, $registerDto->password);
        $user->setPassword($hashedPassword);
        $user->setPhone($registerDto->phone);
        $user->setRoles([Role::ROLE_USER->value]);

        if (isset($registerDto->address)) {
            $user->setAddress($registerDto->address);
        }

        if (isset($registerDto->email)) {
            $user->setEmail($registerDto->email);
        }

        if (isset($registerDto->fullName)) {
            $user->setFullName($registerDto->fullName);
        }

        $this->getEntityManager()->persist($user);

        return $user;
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\UserNotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $user = $this->find($id);

        if (!isset($user)) {
            throw new UserNotFoundException($id);
        }

        $user->setRoles([Role::ROLE_DELETED->value]);

        $this->getEntityManager()->persist($user);
    }

    /**
     * Summary of addProductToFavorite
     * @param \App\Entity\Product $product
     * 
     * @return void
     */
    public function addProductToFavorite(Product $product): void
    {;
        $user = $this->getCurrentUser();
        $user->addFavorite($product);
        $this->getEntityManager()->persist($user);
    }

    /**
     * Summary of removeProductFromFavorite
     * @param \App\Entity\Product $product
     * 
     * @return void
     */
    public function removeProductFromFavorite(Product $product): void
    {
        $user = $this->getCurrentUser();
        $user->removeFavorite($product);
        $this->getEntityManager()->persist($user);
    }

    /**
     * Summary of getCurrentUser
     * @throws \App\Services\Exception\Access\NotAtuheticatedException
     * @throws \App\Services\Exception\NotFound\UserNotFoundByIdentifierException
     * 
     * @return \App\Entity\User
     */
    public function getCurrentUser(): User
    {
        $user = $this->security->getUser();

        if (!$user) {
            throw new NotAtuheticatedException();
        }

        $userPhone = $user->getUserIdentifier();
        $user = $this->findOneBy(['phone' => $userPhone]);

        if (!$user) {
            throw new UserNotFoundByIdentifierException();
        }

        return $user;
    }

    /**
     * Summary of patch
     * @param \App\DTO\User\PatchUserDto $patchUserDto
     * @param \App\Entity\User $user
     * 
     * @return void
     */
    public function patch(PatchUserDto $patchUserDto, User $user): void
    {
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

        $this->getEntityManager()->persist($user);
    }
}
