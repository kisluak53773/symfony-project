<?php

declare(strict_types=1);

namespace App\Services\Validator;

use Symfony\Component\HttpFoundation\Request;

class ProductValidator
{
    public function isProductWithVendorValid(Request $request): bool
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
            return false;
        }

        return true;
    }
}
