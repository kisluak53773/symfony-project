<?php

declare(strict_types=1);

namespace App\Contract\Service;

use Doctrine\Common\Collections\Collection;

interface FavoriteServiceInterface
{
    public function addToFavorite(int $pruductId): void;

    public function getFavoriteProducts(): Collection;

    public function deleteFromFavorite(int $pruductId): void;
}
