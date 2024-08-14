<?php

declare(strict_types=1);

namespace App\DTO\VendorProduct;

use Symfony\Component\Validator\Constraints as Assert;

class PatchVendorProductDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Price should be present')]
        public readonly string $price,

        #[Assert\NotBlank(message: 'Quantity should be present')]
        #[Assert\PositiveOrZero(message: 'Quantity can not be negative')]
        public readonly int $quantity,
    ) {}
}
