<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\DTO\User\PatchUserDto;
use App\Entity\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use App\DTO\Auth\RegisterDto;
use App\Entity\Product;

interface UserRepositoryInterface
{
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void;

    public function registerUser(RegisterDto $registerDto): User;

    public function delete(int $id): void;

    public function addProductToFavorite(Product $product): void;

    public function removeProductFromFavorite(Product $product): void;

    public function getCurrentUser(): User;

    public function patch(PatchUserDto $patchUserDto, User $user): void;
}
