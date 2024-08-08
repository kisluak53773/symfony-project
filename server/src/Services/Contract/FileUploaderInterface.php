<?php

declare(strict_types=1);

namespace App\Services\Contract;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploaderInterface
{
    public function upload(UploadedFile $file): string;

    public function getTargetDirectory(): string;
}
