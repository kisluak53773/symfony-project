<?php

declare(strict_types=1);

namespace App\Services\Validator;

use Symfony\Component\HttpFoundation\Request;

use App\Services\Exception\Request\BadRequsetException;

class ReviewValidator
{
    /**
     * Summary of isReviewValid
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isReviewValid(Request $request): void
    {
        if (
            !isset($request->productId) ||
            !isset($request->rating)
        ) {
            throw new BadRequsetException();
        }
    }

    /**
     * Summary of isReviewValidToPathc
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function isReviewValidToPathc(Request $request): void
    {
        if (!isset($request->rating)) {
            throw new BadRequsetException();
        }
    }
}
