<?php

declare(strict_types=1);

namespace App\Enum;

enum OrderStatus: string
{
    case ORDER_DELIVERED = 'delivered';
    case ORDER_PROCESSED = 'processed';
    case ORDER_ON_THE_WAY = 'on the way';
    case ORDER_CANCELED = 'canceled';
}
