<?php

declare(strict_types=1);

namespace App\Services\Exception\Access;

class NotAllowedToPatchReviewException extends AccessForbiddenException
{
    protected $message = 'You are not allowd to patch this review';
}
