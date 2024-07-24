<?php

declare(strict_types=1);

namespace App\Services\Validator;

class VendorProductValidator
{
    public function validateVendorToPatch(mixed $request): bool
    {
        if (
            !isset($request->quantity) ||
            !isset($request->price)
        ) {
            return false;
        }

        return true;
    }
}
