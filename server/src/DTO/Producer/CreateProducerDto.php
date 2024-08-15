<?php

declare(strict_types=1);

namespace App\DTO\Producer;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProducerDto
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

        #[Assert\NotBlank(message: 'Country of origin should be present')]
        #[Assert\Length(
            min: 2,
            max: 40,
            minMessage: 'Country of origin should not be so short',
            maxMessage: 'Country of origin should not be so long',
        )]
        public readonly string $country,

        #[Assert\NotBlank(message: 'Address method should be present')]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Address should not be so short',
            maxMessage: 'Address should not be so long',
        )]
        public readonly string $address,
    ) {}
}
