<?php

declare(strict_types=1);

namespace App\Services;

use Doctrine\Persistence\ManagerRegistry;
use App\Services\Uploader\ProductImageUploader;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\User;
use App\Entity\Type;
use App\Entity\Producer;
use App\Entity\VendorProduct;
use App\Enum\Role;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\ServerErrorException;
use App\DTO\Product\CreateProductDto;
use App\DTO\Product\ProductSearchParamsDto;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductService
{
    public function __construct(
        private ManagerRegistry $registry,
        private ProductImageUploader $uploader,
        private Security $security,
        private PaginatorInterface $paginator,
        private ProductRepository $productRepository
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
        $entityManager = $this->registry->getManager();
        $user = $this->security->getUser();

        $producer = $entityManager->getRepository(Producer::class)->find($createProductDto->producerId);

        if (!isset($producer)) {
            throw new BadRequsetException('Such producer does not exist');
        }

        $type = $entityManager->getRepository(Type::class)->find($createProductDto->typeId);

        if (!isset($type)) {
            throw new BadRequsetException('Such type does not exist');
        }

        try {
            $imagePath = $this->uploader->upload($image);
        } catch (FileException $e) {
            throw new ServerErrorException($e->getMessage());
        }

        $product = new Product();
        $product->setTitle($createProductDto->title);
        $product->setDescription($createProductDto->description);
        $product->setCompound($createProductDto->compound);
        $product->setStorageConditions($createProductDto->storageConditions);
        $product->setWeight($createProductDto->weight);
        $product->setImage($imagePath);
        $product->setType($type);
        $product->setProducer($producer);
        $entityManager->persist($product);

        if (isset($user) && in_array(Role::ROLE_VENDOR->value, $user->getRoles()) && isset($createProductDto->quantity)) {
            $userPhone = $user->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

            $vendorProduct = new VendorProduct();
            $vendorProduct->setVendor($user->getVendor());
            $vendorProduct->setProduct($product);
            $vendorProduct->setPrice($createProductDto->price);

            if ($createProductDto->quantity) {
                $vendorProduct->setQuantity($createProductDto->quantity);
            }

            $entityManager->persist($vendorProduct);
        }

        $entityManager->flush();

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
        $entityMnager = $this->registry->getManager();

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityMnager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

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
        $entityManager = $this->registry->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!isset($product)) {
            throw new NotFoundHttpException('No such product exist');
        }

        $entityManager->remove($product);
        $entityManager->flush();
    }
}
