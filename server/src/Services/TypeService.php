<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\ServerErrorException;
use App\Services\Uploader\TypesImageUploader;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\DTO\Type\CreatTypeDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class TypeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TypesImageUploader $uploader,
    ) {}

    /**
     * Summary of add
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\ServerErrorException
     * 
     * @return int
     */
    public function add(UploadedFile $image, CreatTypeDto $creatTypeDto): int
    {
        $type = new Type();
        $type->setTitle($creatTypeDto->title);

        try {
            $imagePath = $this->uploader->upload($image);
        } catch (FileException $e) {
            throw new ServerErrorException($e->getMessage());
        }

        $type->setImage($imagePath);

        $this->entityManager->persist($type);
        $this->entityManager->flush();

        return $type->getId();
    }


    /**
     * Summary of get
     * @return array
     */
    public function get(): array
    {
        return $this->entityManager->getRepository(Type::class)->findAll();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $type = $this->entityManager->getRepository(Type::class)->find($id);

        if (!isset($type)) {
            throw new NotFoundException('Type not found');
        }

        $this->entityManager->remove($type);
        $this->entityManager->flush();
    }
}
