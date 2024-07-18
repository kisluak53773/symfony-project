<?php

declare(strict_types=1);

namespace App\Services\Validator;

class UserValidator
{
    public function isUserVendorValid(mixed $request): bool
    {
        if (
            !isset($request->email) ||
            !isset($request->fullName) ||
            !isset($request->address) ||
            !isset($request->password) ||
            !isset($request->phone)
        ) {
            return false;
        }

        return true;
    }
}
