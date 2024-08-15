<?php

declare(strict_types=1);

namespace App\Contract\Service;

use App\DTO\Type\CreatTypeDto;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface TypeServiceInterface
{
    public function add(UploadedFile $image, CreatTypeDto $creatTypeDto): int;

    /**
     * Summary of get
     * @return Type[]
     */
    public function get(): array;

    public function delete(int $id): void;
}
