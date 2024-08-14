<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Exception\Request\NotFoundException;
use App\DTO\Producer\CreateProducerDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\ProducerRepositoryInterface;
use App\Contract\Service\ProducerServiceInterface;

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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
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
     * @return array
     */
    public function getForVendor(): array
    {
        return $this->producerRepository->findAll();
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
        $producer = $this->producerRepository->find($id);

        if (!isset($producer)) {
            throw new NotFoundException('Producer not found');
        }

        $this->producerRepository->remove($producer);
        $this->entityManager->flush();
    }
}
