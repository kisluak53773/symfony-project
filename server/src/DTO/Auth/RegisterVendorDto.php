<?php

declare(strict_types=1);

namespace App\DTO\Auth;


use Symfony\Component\Validator\Constraints as Assert;

class RegisterVendorDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Password should be present')]
        public readonly string $password,

        #[Assert\NotBlank(message: 'Phone should be present')]
        #[Assert\Length(
            min: 1,
            max: 30,
            minMessage: 'Phone can not be so short',
            maxMessage: 'Phone should not be so long',
        )]
        public readonly string $phone,

        #[Assert\NotBlank(message: 'Email should be present')]
        #[Assert\Email(
            message: 'The email is not a valid email.',
        )]
        public readonly string $email,

        #[Assert\NotBlank(message: 'Full name should be present')]
        #[Assert\Length(
            max: 180,
            maxMessage: 'Full name  should not be so long',
        )]
        public readonly string $fullName,

        #[Assert\NotBlank(message: 'Address should be present')]
        #[Assert\Length(
            max: 180,
            maxMessage: 'Address should not be so long',
        )]
        public readonly string $address,

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
        public readonly string $vendorAddress,

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

        #[Assert\NotBlank(message: 'Registration date should be present')]
        #[Assert\Date('Wrong format for registration rate ')]
        public readonly string $registrationDate,

        #[Assert\NotBlank(message: 'Registration date should be present')]
        #[Assert\Date('Wrong format for registration cetificat date ')]
        public readonly string $registrationCertificateDate,
    ) {}
}
