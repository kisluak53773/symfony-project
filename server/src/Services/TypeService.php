<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Type\CreatTypeDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\TypeRepositoryInterface;
use App\Contract\Service\TypeServiceInterface;
use App\Services\Exception\NotFound\TypeNotFoundException;
use App\Contract\FileUploaderInterface;
use App\Entity\Type;

class TypeService implements TypeServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Services\Uploader\TypesImageUploader $uploader
     * @param \App\Repository\TypeRepository $typeRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploaderInterface $uploader,
        private TypeRepositoryInterface $typeRepository
    ) {}

    /**
     * Summary of add
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image
     * @param \App\DTO\Type\CreatTypeDto $creatTypeDto
     * 
     * @return int
     */
    public function add(UploadedFile $image, CreatTypeDto $creatTypeDto): int
    {
        $imagePath = $this->uploader->upload($image);
        $type = $this->typeRepository->create($creatTypeDto, $imagePath);
        $this->entityManager->flush();

        return $type->getId() ?? 0;
    }


    /**
     * Summary of get
     * @return Type[]
     */
    public function get(): array
    {
        return $this->typeRepository->findAll();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\TypeNotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $type = $this->typeRepository->find($id);

        if (!isset($type)) {
            throw new TypeNotFoundException($id);
        }

        $this->typeRepository->remove($type);
        $this->entityManager->flush();
    }
}
