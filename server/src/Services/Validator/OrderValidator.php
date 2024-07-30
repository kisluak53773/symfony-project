<?php

declare(strict_types=1);

namespace App\Services\Validator;

class OrderValidator
{
    public function isValidToCreateOrder(mixed $request): bool
    {
        if (
            !array_key_exists('paymentMethod', $request) ||
            !array_key_exists('products', $request) ||
            !is_array($request['products'])
        ) {
            return false;
        }

        return true;
    }
}
