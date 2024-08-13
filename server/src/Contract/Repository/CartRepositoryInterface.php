<?php

declare(strict_types=1);

namespace App\Contract\Repository;

use App\Entity\User;
use App\Entity\Cart;

interface CartRepositoryInterface
{
    public function create(User $user): Cart;
}
