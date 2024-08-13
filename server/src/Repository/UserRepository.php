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
use App\Services\Exception\Request\NotFoundException;
use Symfony\Bundle\SecurityBundle\Security;
use App\DTO\User\PatchUserDto;
use App\Contract\Repository\UserRepositoryInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface, UserRepositoryInterface
{
    private Security $security;

    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, User::class);

        $this->security = $security;
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

        if (isset($userDto->address)) {
            $user->setAddress($registerDto->address);
        }

        if (isset($userDto->email)) {
            $user->setEmail($registerDto->email);
        }

        if (isset($userDto->fullName)) {
            $user->setFullName($registerDto->fullName);
        }

        $this->getEntityManager()->persist($user);

        return $user;
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
        $user = $this->find($id);

        if (!isset($user)) {
            throw new NotFoundException();
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
     * @return \App\Entity\User
     */
    public function getCurrentUser(): User
    {
        $userPhone = $this->security->getUser()->getUserIdentifier();
        return $this->findOneBy(['phone' => $userPhone]);
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
