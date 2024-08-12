<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\ServerErrorException;
use Doctrine\Persistence\ManagerRegistry;
use App\Services\Uploader\TypesImageUploader;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\Type\CreatTypeDto;

class TypeService
{
    public function __construct(
        private ManagerRegistry $registry,
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
    public function add(Request $request, CreatTypeDto $creatTypeDto): int
    {
        $entityManger = $this->registry->getManager();

        $type = new Type();
        $type->setTitle($creatTypeDto->title);

        if ($request->files->has('image')) {
            try {
                $imagePath = $this->uploader->upload($request->files->get('image'));
                $type->setImage($imagePath);
            } catch (FileException $e) {
                throw new ServerErrorException($e->getMessage());
            }
        }

        $entityManger->persist($type);
        $entityManger->flush();

        return $type->getId();
    }


    /**
     * Summary of get
     * @return array
     */
    public function get(): array
    {
        $entityManager = $this->registry->getManager();

        return $entityManager->getRepository(Type::class)->findAll();
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
        $entityManager = $this->registry->getManager();

        $type = $entityManager->getRepository(Type::class)->find($id);

        if (!isset($type)) {
            throw new NotFoundException('Type not found');
        }

        $entityManager->remove($type);
        $entityManager->flush();
    }
}
