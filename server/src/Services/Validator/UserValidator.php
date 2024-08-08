<?php

declare(strict_types=1);

namespace App\Services\Validator;

use App\Services\Exception\Request\BadRequsetException;

class UserValidator
{
    /**
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isUserVendorValid(mixed $request): void
    {
        if (
            !isset($request->email) ||
            !isset($request->fullName) ||
            !isset($request->address) ||
            !isset($request->password) ||
            !isset($request->phone)
        ) {
            throw new BadRequsetException();
        }
    }
}
