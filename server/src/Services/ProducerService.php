<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\Entity\Producer;

class ProducerService
{
    public function __construct(
        private ManagerRegistry $registry,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Summary of add
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return int
     */
    public function add(Request $request): int
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->title) || !isset($decoded->country) || !isset($decoded->address)) {
            throw new BadRequsetException();
        }

        $title = $decoded->title;
        $country = $decoded->country;
        $address = $decoded->address;

        $producer = new Producer();
        $producer->setTitle($title);
        $producer->setCountry($country);
        $producer->setAddress($address);

        $errors = $this->validator->validate($producer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($producer);
        $entityManager->flush();

        return $producer->getId();
    }

    /**
     * Summary of getForVendor
     * @return array
     */
    public function getForVendor(): array
    {
        $entityManager = $this->registry->getManager();

        return $entityManager->getRepository(Producer::class)->findAll();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function delete(int $id)
    {
        $entityManager = $this->registry->getManager();

        $producer = $entityManager->getRepository(Producer::class)->find($id);

        if (!isset($producer)) {
            throw new NotFoundException('Producer not found');
        }

        $entityManager->remove($producer);
        $entityManager->flush();
    }
}
