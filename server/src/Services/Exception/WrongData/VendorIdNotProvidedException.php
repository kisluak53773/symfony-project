<?php

declare(strict_types=1);

namespace App\Services\Exception\WrongData;

class VendorIdNotProvidedException extends WrongDataException
{
    public function __construct()
    {
        parent::__construct('Vendor id is not provided');
    }
}
