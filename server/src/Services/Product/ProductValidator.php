<?php

declare(strict_types=1);

namespace App\Services\Product;

use Symfony\Component\HttpFoundation\Request;

class ProductValidator
{
    public function validateForCreation(Request $request): bool
    {
        if (
            !$request->request->has('title') ||
            !$request->request->has('description') ||
            !$request->request->has('compound') ||
            !$request->request->has('storageConditions') ||
            !$request->request->has('type') ||
            !$request->request->has('weight') ||
            !$request->request->has('price') ||
            !$request->request->has('producerId') ||
            !$request->files->has('image')
        ) {
            return false;
        }

        return true;
    }
}
