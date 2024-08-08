<?php

declare(strict_types=1);

namespace App\Services\Validator;

use App\Services\Exception\Request\BadRequsetException;

class CartValidator
{
    /**
     * Summary of isValidToAddToCart
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isValidToAddToCart(mixed $request): void
    {
        if (
            !isset($request->quantity) ||
            !isset($request->vendorProductId)
        ) {
            throw new BadRequsetException();
        }
    }

    /**
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isValidToIncreaseAmounInCart(mixed $request): void
    {
        if (
            !isset($request->quantity) ||
            !isset($request->vendorProductId)
        ) {
            throw new BadRequsetException();
        }
    }

    /**
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isValidToDecreaseAmounInCart(mixed $request): void
    {
        if (
            !isset($request->quantity) ||
            !isset($request->vendorProductId)
        ) {
            throw new BadRequsetException();
        }
    }

    /**
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isValidToRemove(mixed $request): void
    {
        if (
            !isset($request->vendorProductId)
        ) {
            throw new BadRequsetException();
        }
    }
}
