<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bundle\SecurityBundle\Security;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\VendorProductRepository;
use App\Enum\Role;
use App\Entity\User;
use App\Entity\VendorProduct;
use App\Entity\Product;
use App\Entity\Vendor;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\DTO\VendorProduct\CreateVendorProductDto;
use App\DTO\VendorProduct\PatchVendorProduct;
use App\DTO\PaginationQueryDto;
use Doctrine\ORM\EntityManagerInterface;

class VendorProductService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
        private PaginatorInterface $paginator,
        private VendorProductRepository $vendorProductRepository,
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
        $user = $this->security->getUser();

        if (isset($user) && in_array(Role::ROLE_VENDOR->value, $user->getRoles())) {
            $userPhone = $user->getUserIdentifier();
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
            $vendor = $user->getVendor();
        } else {
            if (!isset($createVendorProductDto->vendorId)) {
                throw new BadRequsetException();
            }

            $vendorId = $createVendorProductDto->vendorId;
            $vendor = $this->entityManager->getRepository(Vendor::class)->find($vendorId);

            if (!isset($vendor)) {
                throw new NotFoundException('Such vendor does not exist');
            }
        }

        $productId = $createVendorProductDto->productId;
        $product = $this->entityManager->getRepository(Product::class)->find($productId);

        if (!isset($product)) {
            throw new NotFoundException('Such product does not exist');
        }

        $vendorProduct = new VendorProduct();
        $vendorProduct->setPrice($createVendorProductDto->price);
        $vendorProduct->setVendor($vendor);
        $vendorProduct->setProduct($product);

        if (isset($decoded->quantity)) {
            $vendorProduct->setQuantity($createVendorProductDto->quantity);
        }

        $this->entityManager->persist($vendorProduct);
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
        $user = $this->security->getUser();

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['phone' => $user->getUserIdentifier()]);
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
        $vendorProduct = $this->entityManager->getRepository(VendorProduct::class)->find($id);
        $vendorProduct->setPrice($patchVendorProduct->price);
        $vendorProduct->setQuantity($patchVendorProduct->quantity);

        $this->entityManager->persist($vendorProduct);
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

        $this->entityManager->remove($vendorProduct);
        $this->entityManager->flush();
    }
}
