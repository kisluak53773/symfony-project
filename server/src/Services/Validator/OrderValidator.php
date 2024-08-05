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

    public function isValidToPatchOrder(mixed $request): bool
    {
        if (
            !isset($request->paymentMethod) ||
            !isset($request->deliveryTime) ||
            !isset($request->orderStatus)
        ) {
            return false;
        }

        return true;
    }

    public function isValidToPatchVendorOrder(mixed $request): bool
    {
        if (
            !isset($request->orderStatus)
        ) {
            return false;
        }

        return true;
    }
}
