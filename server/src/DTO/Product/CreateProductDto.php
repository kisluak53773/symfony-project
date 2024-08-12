<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProductDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Title should be present')]
        #[Assert\Length(
            min: 2,
            max: 40,
            minMessage: 'Title must not be so short',
            maxMessage: 'Title should not be so long',
        )]
        public readonly string $title,

        #[Assert\NotBlank(message: 'Description should be present')]
        #[Assert\Length(
            min: 1,
            max: 1000,
            minMessage: 'Description must not be so short',
            maxMessage: 'Description should not be so long',
        )]
        public readonly string $description,

        #[Assert\NotBlank(message: 'Compound should be present')]
        #[Assert\Length(
            min: 1,
            max: 1000,
            minMessage: 'Compound must not be so short',
            maxMessage: 'Compound should not be so long',
        )]
        public readonly string $compound,

        #[Assert\NotBlank(message: 'Storage conditions should be present')]
        #[Assert\Length(
            min: 1,
            max: 255,
            minMessage: 'Storage conditions must not be so short',
            maxMessage: 'Storage conditions should not be so long',
        )]
        public readonly string $storageConditions,

        #[Assert\NotBlank(message: 'Weight should be present')]
        #[Assert\Length(
            min: 1,
            max: 40,
            minMessage: 'Weight conditions must not be so short',
            maxMessage: 'Weightshould not be so long',
        )]
        public readonly string $weight,

        #[Assert\NotBlank(message: 'Producer id should be present')]
        public readonly int $producerId,

        #[Assert\NotBlank(message: 'Type id should be present')]
        public readonly int $typeId,

        public readonly string $price,

        #[Assert\PositiveOrZero(message: 'Quantity can not be negative')]
        public readonly int $quantity,
    ) {}
}
