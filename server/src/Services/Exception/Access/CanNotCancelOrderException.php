<?php

declare(strict_types=1);

namespace App\Services\Exception\Access;

class CanNotCancelOrderException extends AccessForbiddenException
{
    public function __construct(int $orderId)
    {
        parent::__construct("You cannot cancel order with id $orderId");
    }
}
