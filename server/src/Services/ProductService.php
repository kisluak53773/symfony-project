<?php

declare(strict_types=1);

namespace App\Services;

use App\Enum\Role;
use App\DTO\Product\CreateProductDto;
use App\DTO\Product\ProductSearchParamsDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use App\Contract\Repository\ProductRepositoryInterface;
use App\Contract\Repository\ProducerRepositoryInterface;
use App\Contract\Repository\UserRepositoryInterface;
use App\Contract\Repository\TypeRepositoryInterface;
use App\Contract\Repository\VendorProductRepositoryInterface;
use App\Contract\Service\ProductServiceIntrrafce;
use App\Contract\PaginationHandlerInterface;
use App\Services\Exception\NotFound\TypeNotFoundException;
use App\Services\Exception\NotFound\ProducerNotFoundException;
use App\Services\Exception\NotFound\ProductNotFoundException;
use App\Contract\FileUploaderInterface;
use App\Entity\Product;
use App\Services\Exception\NotFound\VendorNotFoundException;

class ProductService implements ProductServiceIntrrafce
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Services\Uploader\ProductImageUploader $uploader
     * @param \App\Contract\PaginationHandlerInterface<Product> $paginationHandler
     * @param \App\Repository\ProductRepository $productRepository
     * @param \App\Repository\ProducerRepository $producerRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\TypeRepository $typeRepository
     * @param \App\Repository\VendorProductRepository $vendorProductRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FileUploaderInterface $uploader,
        private PaginationHandlerInterface $paginationHandler,
        private ProductRepositoryInterface $productRepository,
        private ProducerRepositoryInterface $producerRepository,
        private UserRepositoryInterface $userRepository,
        private TypeRepositoryInterface $typeRepository,
        private VendorProductRepositoryInterface $vendorProductRepository,
    ) {}

    /**
     * Summary of addWithVendor
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image
     * @param \App\DTO\Product\CreateProductDto $createProductDto
     * 
     * @throws \App\Services\Exception\NotFound\ProducerNotFoundException
     * @throws \App\Services\Exception\NotFound\TypeNotFoundException
     * @throws \App\Services\Exception\NotFound\VendorNotFoundException
     * 
     * @return int
     */
    public function addWithVendor(UploadedFile $image, CreateProductDto $createProductDto): int
    {
        $user = $this->userRepository->getCurrentUser();
        $producer = $this->producerRepository->find($createProductDto->producerId);

        if (!isset($producer)) {
            throw new ProducerNotFoundException($createProductDto->producerId);
        }

        $type = $this->typeRepository->find($createProductDto->typeId);

        if (!isset($type)) {
            throw new TypeNotFoundException($createProductDto->typeId);
        }

        $imagePath = $this->uploader->upload($image);
        $product = $this->productRepository->create($createProductDto, $type, $producer, $imagePath);

        if (in_array(Role::ROLE_VENDOR->value, $user->getRoles()) && isset($createProductDto->price)) {
            $vendor = $user->getVendor();

            if (!$vendor) {
                throw new VendorNotFoundException();
            }

            $this->vendorProductRepository->create($vendor, $product, $createProductDto->price, $createProductDto->quantity);
        }

        $this->entityManager->flush();

        return $product->getId() ?? 0;
    }

    /**
     * Summary of list
     * @param \App\DTO\Product\ProductSearchParamsDto $productSearchParamsDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function list(ProductSearchParamsDto $productSearchParamsDto): array
    {
        $querryBuilder = $this->productRepository->searchByTitle($productSearchParamsDto);
        $response = $this->paginationHandler->handlePagination($querryBuilder, $productSearchParamsDto);

        return $response;
    }

    /**
     * Summary of getProductsVendorDoesNotSell
     * @param \App\DTO\Product\ProductSearchParamsDto $productSearchParamsDto
     * 
     * @return array{
     *     total_items: int,
     *     current_page: int,
     *     total_pages: int,
     *     data: iterable<int, mixed>
     * }
     */
    public function getProductsVendorDoesNotSell(ProductSearchParamsDto $productSearchParamsDto): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendor = $user->getVendor();

        if (!$vendor) {
            throw new VendorNotFoundException();
        }

        $querryBuilder = $this->productRepository->searchByTitle($productSearchParamsDto, $vendor->getId());
        $response = $this->paginationHandler->handlePagination($querryBuilder, $productSearchParamsDto);

        return $response;
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\NotFound\ProductNotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $product = $this->productRepository->find($id);

        if (!isset($product)) {
            throw new ProductNotFoundException($id);
        }

        $this->productRepository->remove($product);
        $this->entityManager->flush();
    }
}
