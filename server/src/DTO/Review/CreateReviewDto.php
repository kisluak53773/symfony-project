<?php

declare(strict_types=1);

namespace App\DTO\Review;

use Symfony\Component\Validator\Constraints as Assert;

class CreateReviewDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Product id should be present')]
        public readonly int $productId,

        #[Assert\NotBlank(message: 'Rating should be present')]
        #[Assert\Positive(message: 'Rating can not be negative number')]
        public readonly int $rating,

        #[Assert\Length(
            max: 400,
            maxMessage: 'Comment should not be so long',
        )]
        public readonly ?string $comment,
    ) {}
}
