<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\NotFoundException;
use App\Services\Exception\Request\ServerErrorException;
use App\Services\Exception\Request\BadRequsetException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Uploader\TypesImageUploader;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class TypeService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Request $request,
        private TypesImageUploader $uploader,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Summary of add
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\ServerErrorException
     * 
     * @return void
     */
    public function add(): int
    {
        $entityManger = $this->registry->getManager();

        if (!$this->request->request->has('title')) {
            throw new BadRequsetException();
        }

        $type = new Type();
        $type->setTitle($this->request->request->get('title'));

        if ($this->request->files->has('image')) {
            try {
                $imagePath = $this->uploader->upload($this->request->files->get('image'));
                $type->setImage($imagePath);
            } catch (FileException $e) {
                throw new ServerErrorException($e->getMessage());
            }
        }

        $errors = $this->validator->validate($type);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManger->persist($type);
        $entityManger->flush();

        return $type->getId();
    }


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
