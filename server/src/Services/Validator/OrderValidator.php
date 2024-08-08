<?php

declare(strict_types=1);

namespace App\Services\Validator;

use App\Services\Exception\Request\BadRequsetException;

class OrderValidator
{
    /**
     * Summary of isValidToCreateOrder
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isValidToCreateOrder(mixed $request): void
    {
        if (
            !isset($request->paymentMethod) ||
            !isset($request->deliveryTime)
        ) {
            throw new BadRequsetException();
        }
    }

    /**
     * Summary of isValidToPatchOrder
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isValidToPatchOrder(mixed $request): void
    {
        if (
            !isset($request->paymentMethod) ||
            !isset($request->deliveryTime) ||
            !isset($request->orderStatus)
        ) {
            throw new BadRequsetException();
        }
    }

    /**
     * Summary of isValidToPatchVendorOrder
     * @param mixed $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isValidToPatchVendorOrder(mixed $request): void
    {
        if (
            !isset($request->orderStatus)
        ) {
            throw new BadRequsetException();
        }
    }
}
