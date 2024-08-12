<?php

declare(strict_types=1);

namespace App\DTO\Type;

use Symfony\Component\Validator\Constraints as Assert;

class CreatTypeDto
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
    ) {}
}
