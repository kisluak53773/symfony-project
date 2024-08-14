<?php

declare(strict_types=1);

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class PatchUserDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Phone should be present')]
        #[Assert\Length(
            min: 1,
            max: 30,
            minMessage: 'Phone can not be so short',
            maxMessage: 'Phone should not be so long',
        )]
        public readonly string $phone,

        #[Assert\Email(
            message: 'The email is not a valid email.',
        )]
        public readonly ?string $email,

        #[Assert\Length(
            max: 180,
            maxMessage: 'Full name  should not be so long',
        )]
        public readonly ?string $fullName,

        #[Assert\Length(
            max: 180,
            maxMessage: 'Address should not be so long',
        )]
        public readonly ?string $address,
    ) {}
}
