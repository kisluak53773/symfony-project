<?php

declare(strict_types=1);

namespace App\Services\Validator;

class VendorValidator
{
    public function isVendorValid(mixed $request): bool
    {
        if (
            !isset($request->title) ||
            !isset($request->vendorAddress) ||
            !isset($request->inn) ||
            !isset($request->registrationAuthority) ||
            !isset($request->registrationDate) ||
            !isset($request->registrationCertificateDate)
        ) {
            return false;
        }

        return true;
    }

    public function isVendorValidForPatch(mixed $request): bool
    {
        if (
            !isset($request->title) ||
            !isset($request->address) ||
            !isset($request->inn) ||
            !isset($request->registrationAuthority)
        ) {
            return false;
        }

        return true;
    }
}
