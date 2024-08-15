<?php

declare(strict_types=1);

namespace App\Contract\Service;

use Doctrine\Common\Collections\Collection;
use App\Entity\Product;

interface FavoriteServiceInterface
{
    public function addToFavorite(int $pruductId): void;

    /**
     * @return Collection<int, Product>
     */
    public function getFavoriteProducts(): Collection;

    public function deleteFromFavorite(int $pruductId): void;
}
