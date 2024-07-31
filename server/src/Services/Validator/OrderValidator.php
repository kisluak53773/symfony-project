<?php

declare(strict_types=1);

namespace App\Services\Validator;

class OrderValidator
{
    public function isValidToCreateOrder(mixed $request): bool
    {
        if (
            !isset($request->paymentMethod) ||
            !isset($request->deliveryTime)
        ) {
            return false;
        }

        return true;
    }
}
