<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Auth\RegisterDto;

interface AuthServiceInterface
{
    public function register(RegisterDto $registerDto): void;

    public function delete(int $id): void;
}
