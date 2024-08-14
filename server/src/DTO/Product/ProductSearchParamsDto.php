<?php

declare(strict_types=1);

namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;
use App\DTO\PaginationQueryDto;

class ProductSearchParamsDto extends PaginationQueryDto
{
    public function __construct(
        #[Assert\Positive("Page can not be a negative number")]
        public readonly int $limit = 5,

        #[Assert\Positive("Page can not be a negative number")]
        public readonly int $page = 1,

        #[Assert\Length(
            max: 40,
            maxMessage: 'Title of prduct can not be so long',
        )]
        public readonly string $title = '',

        public readonly string $priceSort,

        public readonly array $types,

        public readonly array $producers,
    ) {
        parent::__construct($limit, $page);
    }
}
