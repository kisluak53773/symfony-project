<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Type;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\DTO\Type\CreatTypeDto;
use App\Contract\Repository\TypeRepositoryInterface;

/**
 * @extends ServiceEntityRepository<Type>
 */
class TypeRepository extends ServiceEntityRepository implements TypeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Type::class);
    }

    /**
     * Summary of create
     * @param \App\DTO\Type\CreatTypeDto $creatTypeDto
     * @param string $imagePath
     * 
     * @return \App\Entity\Type
     */
    public function create(CreatTypeDto $creatTypeDto, string $imagePath): Type
    {
        $type = new Type();
        $type->setTitle($creatTypeDto->title);
        $type->setImage($imagePath);

        $this->getEntityManager()->persist($type);

        return $type;
    }

    /**
     * Summary of remove
     * @param \App\Entity\Type $type
     * 
     * @return void
     */
    public function remove(Type $type): void
    {
        $this->getEntityManager()->remove($type);
    }
}
