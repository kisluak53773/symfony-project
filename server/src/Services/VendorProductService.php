<?php

declare(strict_types=1);

namespace App\Services;

use Knp\Component\Pager\PaginatorInterface;
use App\Enum\Role;
use App\Entity\VendorProduct;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\DTO\VendorProduct\CreateVendorProductDto;
use App\DTO\VendorProduct\PatchVendorProduct;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\VendorProductRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\VendorRepositoryInterface;
use App\Contract\Repository\ProductRepositoryInterface;

class VendorProductService
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @param \App\Repository\VendorProductRepository $vendorProductRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\VendorRepository $vendorRepository
     * @param \App\Repository\ProductRepository $productRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PaginatorInterface $paginator,
        private VendorProductRepositoryInterface $vendorProductRepository,
        private UserRepositoryInterface $userRepository,
        private VendorRepositoryInterface $vendorRepository,
        private ProductRepositoryInterface $productRepository,
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
    public function add(CreateVendorProductDto $createVendorProductDto): int
    {
        $user = $this->userRepository->getCurrentUser();

        if (isset($user) && in_array(Role::ROLE_VENDOR->value, $user->getRoles())) {
            $vendor = $user->getVendor();
        } else {
            if (!isset($createVendorProductDto->vendorId)) {
                throw new BadRequsetException();
            }

            $vendor = $this->vendorRepository->find($createVendorProductDto->vendorId);

            if (!isset($vendor)) {
                throw new NotFoundException('Such vendor does not exist');
            }
        }

        $product = $this->productRepository->find($createVendorProductDto->productId);

        if (!isset($product)) {
            throw new NotFoundException('Such product does not exist');
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
     */
    public function get(PaginationQueryDto $paginationQueryDto): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        $querryBuilder = $this->vendorProductRepository->createQueryBuilderForPaginationWithVendor($vendor);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $paginationQueryDto->page,
            $paginationQueryDto->limit
        );

        $vendorProducts = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $vendorProducts,
        ];

        return $response;
    }

    /**
     * Summary of patchVendorProdut
     * @param int $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function patchVendorProdut(int $id, PatchVendorProduct $patchVendorProduct): void
    {
        $vendorProduct = $this->vendorProductRepository->find($id);
        $this->vendorProductRepository->patch($patchVendorProduct, $vendorProduct);

        $this->entityManager->flush();
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
        $vendorProduct = $this->entityManager->getRepository(VendorProduct::class)->find($id);

        if (!isset($vendorProduct)) {
            throw new NotFoundException('Vendor does not sell this product');
        }

        $this->vendorProductRepository->remove($vendorProduct);
        $this->entityManager->flush();
    }
}
