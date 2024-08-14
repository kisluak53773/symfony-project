<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\Producer\CreateProducerDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\ProducerRepositoryInterface;
use App\Contract\Service\ProducerServiceInterface;
use App\Entity\Producer;
use App\Services\Exception\NotFound\ProducerNotFoundException;

class ProducerService implements ProducerServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\ProducerRepository $producerRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProducerRepositoryInterface $producerRepository,
    ) {}

    /**
     * Summary of add
     * @param \App\DTO\Producer\CreateProducerDto $createProducerDto
     * 
     * @return int
     */
    public function add(CreateProducerDto $createProducerDto): int
    {
        $producer = $this->producerRepository->create($createProducerDto);
        $this->entityManager->flush();

        return $producer->getId();
    }

    /**
     * Summary of getForVendor
     * @return Producer[]
     */
    public function getForVendor(): array
    {
        return $this->producerRepository->findAll();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\ProducerNotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $producer = $this->producerRepository->find($id);

        if (!isset($producer)) {
            throw new ProducerNotFoundException($id);
        }

        $this->producerRepository->remove($producer);
        $this->entityManager->flush();
    }
}
