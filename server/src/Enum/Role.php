<?php

declare(strict_types=1);

namespace App\Enum;

enum Role: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';
    case ROLE_VENDOR = 'ROLE_VENDOR';
    case ROLE_DELETED = 'ROLE_DELETED';
}
