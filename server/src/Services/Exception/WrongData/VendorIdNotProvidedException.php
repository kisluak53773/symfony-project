<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class VendorIdNotProvidedException extends WrongDataException
{
    protected $message = 'Vendor id is not provided';
}
