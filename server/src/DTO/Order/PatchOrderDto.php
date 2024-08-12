<?php

declare(strict_types=1);

namespace App\DTO\Order;

use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\PaymentMethod;
use App\Enum\OrderStatus;

class PatchOrderDto
{
    public function __construct(
        #[Assert\NotBlank(message: 'Payment method should be present')]
        #[Assert\Choice([PaymentMethod::PAYMENT_CASH->value, PaymentMethod::PAYMENT_CARD->value])]
        public readonly string $paymentMethod,

        #[Assert\NotBlank(message: 'Delivery time should be present')]
        #[Assert\DateTime('Wrong format for delivery time ')]
        public readonly string $deliveryTime,

        #[Assert\NotBlank(message: 'Order status should be present')]
        #[Assert\Choice([
            OrderStatus::ORDER_PROCESSED->value,
            OrderStatus::ORDER_ON_THE_WAY->value,
            OrderStatus::ORDER_DELIVERED->value,
            OrderStatus::ORDER_CANCELED->value
        ])]
        public readonly string $orderStatus,

        #[Assert\Length(
            max: 255,
            maxMessage: 'Comment should not be so long',
        )]
        public readonly string $comment,
    ) {}
}
