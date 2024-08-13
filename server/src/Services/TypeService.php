<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\ServerErrorException;
use App\Services\Uploader\TypesImageUploader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\DTO\Type\CreatTypeDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\TypeRepositoryInterface;

class TypeService
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Services\Uploader\TypesImageUploader $uploader
     * @param \App\Repository\TypeRepository $typeRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TypesImageUploader $uploader,
        private TypeRepositoryInterface $typeRepository
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

        try {
            $imagePath = $this->uploader->upload($image);
        } catch (FileException $e) {
            throw new ServerErrorException($e->getMessage());
        }

        $type = $this->typeRepository->create($creatTypeDto, $imagePath);
        $this->entityManager->flush();

        return $type->getId();
    }


    /**
     * Summary of get
     * @return array
     */
    public function get(): array
    {
        return $this->typeRepository->findAll();
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
        $type = $this->typeRepository->find($id);

        if (!isset($type)) {
            throw new NotFoundException('Type not found');
        }

        $this->typeRepository->remove($type);
        $this->entityManager->flush();
    }
}
