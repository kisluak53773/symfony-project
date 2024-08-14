<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\Role;
use App\Entity\VendorProduct;
use App\DTO\VendorProduct\CreateVendorProductDto;
use App\DTO\VendorProduct\PatchVendorProductDto;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\VendorProductRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\VendorRepositoryInterface;
use App\Contract\Repository\ProductRepositoryInterface;
use App\Contract\Service\VendorProductServiceInterface;
use App\Contract\PaginationHandlerInterface;
use App\Services\Exception\NotFound\VendorNotFoundException;
use App\Services\Exception\WrongData\VendorIdNotProvidedException;
use App\Services\Exception\NotFound\ProductNotFoundException;
use App\Services\Exception\NotFound\VendorProductNotFoundException;

class VendorProductService implements VendorProductServiceInterface
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Services\PaginationHandler $paginationHandler
     * @param \App\Repository\VendorProductRepository $vendorProductRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\VendorRepository $vendorRepository
     * @param \App\Repository\ProductRepository $productRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginationHandlerInterface $paginationHandler,
        private VendorProductRepositoryInterface $vendorProductRepository,
        private UserRepositoryInterface $userRepository,
        private VendorRepositoryInterface $vendorRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    /**
     * Summary of add
     * @param \App\DTO\VendorProduct\CreateVendorProductDto $createVendorProductDto
     * 
     * @throws \App\Services\Exception\WrongData\VendorIdNotProvidedException
     * @throws \App\Services\Exception\NotFound\VendorNotFoundException
     * @throws \App\Services\Exception\NotFound\ProductNotFoundException
     * 
     * @return int
     */
    public function add(CreateVendorProductDto $createVendorProductDto): int
    {
        $user = $this->userRepository->getCurrentUser();

        if (isset($user) && in_array(Role::ROLE_VENDOR->value, $user->getRoles())) {
            $vendor = $user->getVendor();
        } else {
            if (!isset($createVendorProductDto->vendorId)) {
                throw new VendorIdNotProvidedException();
            }

            $vendor = $this->vendorRepository->find($createVendorProductDto->vendorId);

            if (!isset($vendor)) {
                throw new VendorNotFoundException($createVendorProductDto->vendorId);
            }
        }

        $product = $this->productRepository->find($createVendorProductDto->productId);

        if (!isset($product)) {
            throw new ProductNotFoundException($createVendorProductDto->productId);
        }

        $vendorProduct = $this->vendorProductRepository->create(
            $vendor,
            $product,
            $createVendorProductDto->price,
            $createVendorProductDto->quantity
        );
        $this->entityManager->flush();

        return $vendorProduct->getId();
    }

    /**
     * Summary of get
     * @param \App\DTO\PaginationQueryDto $paginationQueryDto
     * 
     * @return array{total_items: int, current_page: int, total_pages: int, data: array}
     */
    public function get(PaginationQueryDto $paginationQueryDto): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        $querryBuilder = $this->vendorProductRepository->createQueryBuilderForPaginationWithVendor($vendor);
        $response = $this->paginationHandler->handlePagination($querryBuilder, $paginationQueryDto);

        return $response;
    }

    /**
     * Summary of patchVendorProdut
     * @param int $id
     * @param \App\DTO\VendorProduct\PatchVendorProductDto $patchVendorProductDto
     * 
     * @return void
     */
    public function patchVendorProdut(int $id, PatchVendorProductDto $patchVendorProductDto): void
    {
        $vendorProduct = $this->vendorProductRepository->find($id);
        $this->vendorProductRepository->patch($patchVendorProductDto, $vendorProduct);

        $this->entityManager->flush();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\VendorProductNotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $vendorProduct = $this->entityManager->getRepository(VendorProduct::class)->find($id);

        if (!isset($vendorProduct)) {
            throw new VendorProductNotFoundException($id);
        }

        $this->vendorProductRepository->remove($vendorProduct);
        $this->entityManager->flush();
    }
}
