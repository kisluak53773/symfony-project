<?php

declare(strict_types=1);

namespace App\Services\Validator;

use App\Services\Exception\Request\BadRequsetException;

class VendorProductValidator
{
    /**
     * Summary of validateVendorToPatch
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function validateVendorToPatch(mixed $request): void
    {
        if (
            !isset($request->quantity) ||
            !isset($request->price)
        ) {
            throw new BadRequsetException();
        }
    }
}
