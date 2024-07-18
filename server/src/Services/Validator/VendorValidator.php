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
}
