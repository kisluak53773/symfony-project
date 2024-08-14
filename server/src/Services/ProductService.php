<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Uploader\ProductImageUploader;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Enum\Role;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\ServerErrorException;
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

class ProductService implements ProductServiceIntrrafce
{
    /**
     * Summary of __construct
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Services\Uploader\ProductImageUploader $uploader
     * @param \Knp\Component\Pager\PaginatorInterface $paginator
     * @param \App\Repository\ProductRepository $productRepository
     * @param \App\Repository\ProducerRepository $producerRepository
     * @param \App\Repository\UserRepository $userRepository
     * @param \App\Repository\TypeRepository $typeRepository
     * @param \App\Repository\VendorProductRepository $vendorProductRepository
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductImageUploader $uploader,
        private PaginatorInterface $paginator,
        private ProductRepositoryInterface $productRepository,
        private ProducerRepositoryInterface $producerRepository,
        private UserRepositoryInterface $userRepository,
        private TypeRepositoryInterface $typeRepository,
        private VendorProductRepositoryInterface $vendorProductRepository,
    ) {}

    /**
     * Summary of addWithVendor
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\ServerErrorException
     * 
     * @return int
     */
    public function addWithVendor(UploadedFile $image, CreateProductDto $createProductDto): int
    {
        $user = $this->userRepository->getCurrentUser();
        $producer = $this->producerRepository->find($createProductDto->producerId);

        if (!isset($producer)) {
            throw new BadRequsetException('Such producer does not exist');
        }

        $type = $this->typeRepository->find($createProductDto->typeId);

        if (!isset($type)) {
            throw new BadRequsetException('Such type does not exist');
        }

        try {
            $imagePath = $this->uploader->upload($image);
        } catch (FileException $e) {
            throw new ServerErrorException($e->getMessage());
        }

        $product = $this->productRepository->create($createProductDto, $type, $producer, $imagePath);

        if (isset($user) && in_array(Role::ROLE_VENDOR->value, $user->getRoles()) && isset($createProductDto->quantity)) {
            $this->vendorProductRepository->create($user->getVendor(), $product, $createProductDto->price, $createProductDto->quantity);
        }

        $this->entityManager->flush();

        return $product->getId();
    }

    /**
     * Summary of list
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
     */
    public function list(ProductSearchParamsDto $productSearchParamsDto): array
    {
        $querryBuilder = $this->productRepository->searchByTitle($productSearchParamsDto);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $productSearchParamsDto->page,
            $productSearchParamsDto->limit
        );

        $products = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $products,
        ];

        return $response;
    }

    /**
     * Summary of getProductsVendorDoesNotSell
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
     */
    public function getProductsVendorDoesNotSell(ProductSearchParamsDto $productSearchParamsDto): array
    {
        $user = $this->userRepository->getCurrentUser();
        $vendorId = $user->getVendor()->getId();
        $querryBuilder = $this->productRepository->searchByTitle($productSearchParamsDto, $vendorId);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $productSearchParamsDto->page,
            $productSearchParamsDto->limit
        );

        $products = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $products,
        ];

        return $response;
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $product = $this->productRepository->find($id);

        if (!isset($product)) {
            throw new NotFoundHttpException('No such product exist');
        }

        $this->producerRepository->remove($product);
        $this->entityManager->flush();
    }
}
