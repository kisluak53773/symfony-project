<?php

declare(strict_types=1);

namespace App\Services\Validator;

use Symfony\Component\HttpFoundation\Request;

use App\Services\Exception\Request\BadRequsetException;

class ProductValidator
{
    /**
     * Summary of isProductWithVendorValid
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isProductWithVendorValid(Request $request): void
    {
        if (
            !$request->request->has('title') ||
            !$request->request->has('description') ||
            !$request->request->has('compound') ||
            !$request->request->has('storageConditions') ||
            !$request->request->has('weight') ||
            !$request->request->has('producerId') ||
            !$request->request->has('typeId') ||
            !$request->files->has('image')
        ) {
            throw new BadRequsetException();
        }
    }
}
