<?php

declare(strict_types=1);

namespace App\DTO\VendorProduct;

use Symfony\Component\Validator\Constraints as Assert;

class CreateVendorProductDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Price should be present')]
        public readonly string $price,

        #[Assert\NotBlank(message: 'Product Id should be present')]
        public readonly int $productId,

        public readonly int $vendorId,

        #[Assert\PositiveOrZero(message: 'Quantity can not be negative')]
        public readonly int $quantity,
    ) {}
}
