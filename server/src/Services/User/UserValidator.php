<?php

declare(strict_types=1);

namespace App\Services\User;

class UserValidator
{
    public function isVendorValid(mixed $request): bool
    {
        if (
            !isset($request->password) ||
            !isset($request->phone) ||
            !isset($request->address) ||
            !isset($request->email) ||
            !isset($request->fullName)
        ) {
            return false;
        }

        return true;
    }
}
