<?php

declare(strict_types=1);

namespace App\Services\Validator;

class CartValidator
{
    public function isValidToAddToCart(mixed $request): bool
    {
        if (
            !$request->request->has('quantity') ||
            !$request->request->has('vendorProductId')
        ) {
            return false;
        }

        return true;
    }

    public function isValidToIncreaseAmounInCart(mixed $request): bool
    {
        if (
            !$request->request->has('quantity') ||
            !$request->request->has('vendorProductId')
        ) {
            return false;
        }

        return true;
    }

    public function isValidToDecreaseAmounInCart(mixed $request): bool
    {
        if (
            !$request->request->has('quantity') ||
            !$request->request->has('vendorProductId')
        ) {
            return false;
        }

        return true;
    }

    public function isValidToRemove(mixed $request): bool
    {
        if (
            !$request->request->has('vendorProductId')
        ) {
            return false;
        }

        return true;
    }
}
