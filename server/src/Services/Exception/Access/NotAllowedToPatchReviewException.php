<?php

declare(strict_types=1);

namespace App\Services\Exception\Access;

class NotAllowedToPatchReviewException extends AccessForbiddenException
{
    public function __construct()
    {
        parent::__construct('You are not allowed to patch this review');
    }
}
