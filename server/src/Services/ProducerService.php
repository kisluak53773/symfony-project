<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Services\Exception\Request\NotFoundException;
use App\Entity\Producer;
use App\DTO\Producer\CreateProducerDto;

class ProducerService
{
    public function __construct(
        private ManagerRegistry $registry,
    ) {}

    /**
     * Summary of add
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return int
     */
    public function add(CreateProducerDto $createProducerDto): int
    {
        $entityManager = $this->registry->getManager();

        $producer = new Producer();
        $producer->setTitle($createProducerDto->title);
        $producer->setCountry($createProducerDto->country);
        $producer->setAddress($createProducerDto->address);

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
