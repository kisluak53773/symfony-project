<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use App\Services\Exception\Request\NotFoundException;
use App\Entity\User;
use App\Entity\Vendor;
use DateTimeImmutable;
use App\Enum\Role;
use App\DTO\Vendor\CreateVendorDto;
use App\DTO\Vendor\PatchVendorDto;

class VendorService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Security $security,
    ) {}

    /**
     * Summary of add
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return int
     */
    public function add(CreateVendorDto $createVendorDto): int
    {
        $entityManager = $this->registry->getManager();

        $userId = $createVendorDto->userId;
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!isset($user)) {
            throw new NotFoundException('Such user does not exist');
        }

        $registraionDate = DateTimeImmutable::createFromFormat('Y-m-d', $createVendorDto->registrationDate);
        $registrationCertificateDate = DateTimeImmutable::createFromFormat('Y-m-d', $createVendorDto->registrationCertificateDate);

        $vendor = new Vendor();
        $vendor->setTitle($createVendorDto->title);
        $vendor->setAddress($createVendorDto->address);
        $vendor->setInn($createVendorDto->inn);
        $vendor->setRegistrationAuthority($createVendorDto->registrationAuthority);
        $vendor->setRegistrationDate($registraionDate);
        $vendor->setRegistrationCertificateDate($registrationCertificateDate);
        $vendor->setUser($user);
        $entityManager->persist($vendor);

        $user->setRoles([Role::ROLE_VENDOR->value]);
        $entityManager->persist($user);

        $entityManager->flush();

        return $vendor->getId();
    }

    /**
     * Summary of getCurrentVendor
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return \App\Entity\Vendor
     */
    public function getCurrentVendor(): Vendor
    {
        $entityManager = $this->registry->getManager();
        $userPhone = $this->security->getUser()->getUserIdentifier();

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $vendor = $user->getVendor();

        if (!isset($vendor)) {
            throw new NotFoundException();
        }

        return $vendor;
    }

    /**
     * Summary of patchCurrentVendor
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function patchCurrentVendor(PatchVendorDto $patchVendorDto): void
    {
        $entityManager = $this->registry->getManager();

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $vendor = $user->getVendor();
        $vendor->setTitle($patchVendorDto->title);
        $vendor->setAddress($patchVendorDto->address);
        $vendor->setInn($patchVendorDto->inn);
        $vendor->setRegistrationAuthority($patchVendorDto->registrationAuthority);

        $entityManager->persist($vendor);
        $entityManager->flush();
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

        $vendor = $entityManager->getRepository(Vendor::class)->find($id);

        if (!isset($vendor)) {
            throw new NotFoundException('No such vendor exist');
        }

        $entityManager->remove($vendor);
        $entityManager->flush();
    }
}
