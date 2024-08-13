<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Producer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\DTO\Producer\CreateProducerDto;
use App\Contract\Repository\ProducerRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Producer>
 */
class ProducerRepository extends ServiceEntityRepository implements ProducerRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producer::class);
    }

    /**
     * Summary of create
     * @param \App\DTO\Producer\CreateProducerDto $createProducerDto
     * 
     * @return \App\Entity\Producer
     */
    public function create(CreateProducerDto $createProducerDto): Producer
    {
        $producer = new Producer();
        $producer->setTitle($createProducerDto->title);
        $producer->setCountry($createProducerDto->country);
        $producer->setAddress($createProducerDto->address);

        $this->getEntityManager()->persist($producer);

        return $producer;
    }

    public function remove(Producer $producer): void
    {
        $this->getEntityManager()->remove($producer);
    }
}
