<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Vendor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\DTO\Vendor\CreateVendorDto;
use App\Enum\Role;
use DateTimeImmutable;
use App\DTO\Vendor\PatchVendorDto;
use App\Contract\Repository\VendorRepositoryInterface;
use App\Services\Exception\WrongData\WrongDateFormmatException;

/**
 * @extends ServiceEntityRepository<Vendor>
 */
class VendorRepository extends ServiceEntityRepository implements VendorRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vendor::class);
    }

    /**
     * Summary of create
     * @param \App\DTO\Vendor\CreateVendorDto $createVendorDto
     * @param \App\Entity\User $user
     * 
     * @return \App\Entity\Vendor
     */
    public function create(CreateVendorDto $createVendorDto, User $user): Vendor
    {
        $registraionDate = DateTimeImmutable::createFromFormat('Y-m-d', $createVendorDto->registrationDate);
        $registrationCertificateDate = DateTimeImmutable::createFromFormat('Y-m-d', $createVendorDto->registrationCertificateDate);

        if (!$registraionDate || !$registrationCertificateDate) {
            throw new WrongDateFormmatException();
        }

        $vendor = new Vendor();
        $vendor->setTitle($createVendorDto->title);
        $vendor->setAddress($createVendorDto->address);
        $vendor->setInn($createVendorDto->inn);
        $vendor->setRegistrationAuthority($createVendorDto->registrationAuthority);
        $vendor->setRegistrationDate($registraionDate);
        $vendor->setRegistrationCertificateDate($registrationCertificateDate);
        $vendor->setUser($user);
        $this->getEntityManager()->persist($vendor);

        $user->setRoles([Role::ROLE_VENDOR->value]);
        $this->getEntityManager()->persist($user);

        return $vendor;
    }

    /**
     * Summary of patch
     * @param \App\DTO\Vendor\PatchVendorDto $patchVendorDto
     * @param \App\Entity\Vendor $vendor
     * 
     * @return void
     */
    public function patch(PatchVendorDto $patchVendorDto, Vendor $vendor): void
    {
        $vendor->setTitle($patchVendorDto->title);
        $vendor->setAddress($patchVendorDto->address);
        $vendor->setInn($patchVendorDto->inn);
        $vendor->setRegistrationAuthority($patchVendorDto->registrationAuthority);

        $this->getEntityManager()->persist($vendor);
    }

    /**
     * Summary of remove
     * @param \App\Entity\Vendor $vendor
     * 
     * @return void
     */
    public function remove(Vendor $vendor): void
    {
        $this->getEntityManager()->remove($vendor);
    }
}
