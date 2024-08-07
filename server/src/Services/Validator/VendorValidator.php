<?php

declare(strict_types=1);

namespace App\Services\Validator;

use App\Services\Exception\Request\BadRequsetException;

class VendorValidator
{
    /**
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isVendorValid(mixed $request): void
    {
        if (
            !isset($request->title) ||
            !isset($request->vendorAddress) ||
            !isset($request->inn) ||
            !isset($request->registrationAuthority) ||
            !isset($request->registrationDate) ||
            !isset($request->registrationCertificateDate)
        ) {
            throw new BadRequsetException();
        }
    }

    /**
     * Summary of isVendorValidForPatch
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isVendorValidForPatch(mixed $request): void
    {
        if (
            !isset($request->title) ||
            !isset($request->address) ||
            !isset($request->inn) ||
            !isset($request->registrationAuthority)
        ) {
            throw new BadRequsetException();
        }
    }
}
