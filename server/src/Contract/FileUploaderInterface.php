<?php

declare(strict_types=1);

namespace App\Contract;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileUploaderInterface
{
    public function upload(UploadedFile $file): string;

    public function getTargetDirectory(): string;
}
