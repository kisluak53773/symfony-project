<?php

declare(strict_types=1);

namespace App\DTO\Order;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\PaymentMethod;

class CreateOrderDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Payment method should be present')]
        #[Assert\Choice([PaymentMethod::PAYMENT_CASH->value, PaymentMethod::PAYMENT_CARD->value])]
        public readonly string $paymentMethod,

        #[Assert\NotBlank(message: 'Delivery time should be present')]
        #[Assert\DateTime(
            format: 'Y-m-d\TH:i',
            message: 'Wrong format for delivery time, the correct format is Y-m-d\TH:i.'
        )]
        public readonly string $deliveryTime,

        #[Assert\Length(
            max: 255,
            maxMessage: 'Comment should not be so long',
        )]
        public readonly ?string $comment,
    ) {}
}
