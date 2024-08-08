<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentMethod: string
{
    case PAYMENT_CASH = 'in cash';
    case PAYMENT_CARD = 'by card';
}
