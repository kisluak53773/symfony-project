<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\User\PatchUserDto;
use Symfony\Component\Security\Core\User\UserInterface;

interface UserServiceInterface
{
    public function getCurrentUser(): UserInterface;

    public function patchCurrentUser(PatchUserDto $patchUserDto): void;
}
