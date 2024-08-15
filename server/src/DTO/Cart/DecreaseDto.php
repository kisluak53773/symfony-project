<?php

declare(strict_types=1);

namespace App\DTO\Cart;

use Symfony\Component\Validator\Constraints as Assert;

class DecreaseDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Quantity should be present')]
        public readonly int $quantity,

        #[Assert\NotBlank(message: 'VendorProductId should be present')]
        public readonly int $vendorProductId,
    ) {}
}
