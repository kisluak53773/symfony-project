<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\NotFoundException;
use App\Entity\Producer;
use App\DTO\Producer\CreateProducerDto;
use Doctrine\ORM\EntityManagerInterface;

class ProducerService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
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
        $producer = new Producer();
        $producer->setTitle($createProducerDto->title);
        $producer->setCountry($createProducerDto->country);
        $producer->setAddress($createProducerDto->address);

        $this->entityManager->persist($producer);
        $this->entityManager->flush();

        return $producer->getId();
    }

    /**
     * Summary of getForVendor
     * @return array
     */
    public function getForVendor(): array
    {
        return $this->entityManager->getRepository(Producer::class)->findAll();
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
        $producer = $this->entityManager->getRepository(Producer::class)->find($id);

        if (!isset($producer)) {
            throw new NotFoundException('Producer not found');
        }

        $this->entityManager->remove($producer);
        $this->entityManager->flush();
    }
}
