<?php

declare(strict_types=1);

namespace App\DTO\Vendor;

use Symfony\Component\Validator\Constraints as Assert;

class PatchVendorDto
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

        #[Assert\NotBlank(message: 'Address should be present')]
        #[Assert\Length(
            min: 2,
            max: 255,
            minMessage: 'Address must not be so short',
            maxMessage: 'Address should not be so long',
        )]
        public readonly string $address,

        #[Assert\NotBlank(message: 'INN should be present')]
        #[Assert\Length(
            min: 2,
            max: 10,
            minMessage: 'INN must not be so short',
            maxMessage: 'INN should not be so long',
        )]
        public readonly string $inn,

        #[Assert\NotBlank(message: 'Registration authority should be present')]
        #[Assert\Length(
            min: 2,
            max: 100,
            minMessage: 'Registration authority must not be so short',
            maxMessage: 'Registration authority should not be so long',
        )]
        public readonly string $registrationAuthority,
    ) {}
}
