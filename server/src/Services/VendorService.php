<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Vendor;
use App\DTO\Vendor\CreateVendorDto;
use App\DTO\Vendor\PatchVendorDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\VendorRepositoryInterface;
use App\Contract\Service\VendorServiceInterface;
use App\Services\Exception\NotFound\UserNotFoundException;
use App\Services\Exception\NotFound\VendorNotFoundException;

class VendorService implements VendorServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\VendorRepository $vendorRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepositoryInterface $userRepository,
        private VendorRepositoryInterface $vendorRepository,
    ) {}

    /**
     * Summary of add
     * @param \App\DTO\Vendor\CreateVendorDto $createVendorDto
     * 
     * @throws \App\Services\Exception\NotFound\UserNotFoundException
     * 
     * @return int
     */
    public function add(CreateVendorDto $createVendorDto): int
    {
        $userId = $createVendorDto->userId;
        $user = $this->userRepository->find($userId);

        if (!isset($user)) {
            throw new UserNotFoundException($userId);
        }

        $vendor = $this->vendorRepository->create($createVendorDto, $user);
        $this->entityManager->flush();

        return $vendor->getId();
    }

    /**
     * Summary of getCurrentVendor
     * @throws \App\Services\Exception\NotFound\VendorNotFoundException
     * 
     * @return \App\Entity\Vendor
     */
    public function getCurrentVendor(): Vendor
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        if (!isset($vendor)) {
            throw new VendorNotFoundException(1);
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
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        $this->vendorRepository->patch($patchVendorDto, $vendor);
        $this->entityManager->flush();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\VendorNotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $vendor = $this->vendorRepository->find($id);

        if (!isset($vendor)) {
            throw new VendorNotFoundException($id);
        }

        $this->vendorRepository->remove($vendor);
        $this->entityManager->flush();
    }
}
