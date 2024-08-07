<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Services\Uploader\ProductImageUploader;
use App\Services\Validator\ProductValidator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

class ProductService
{
    public function __construct(
        private Request $request,
        private ManagerRegistry $registry,
        private ProductImageUploader $uploader,
        private ProductValidator $productValidator,
        private Security $security,
        private ValidatorInterface $validator,
        private PaginatorInterface $paginator,
        private ProductRepository $productRepository
    ) {
    }

    /**
     * Summary of addWithVendor
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\ServerErrorException
     * 
     * @return int
     */
    public function addWithVendor(): int
    {
        $entityManager = $this->registry->getManager();
        $user = $this->security->getUser();

        $this->productValidator->isProductWithVendorValid($this->request);

        $producerId = $this->request->request->get('producerId');
        $producer = $entityManager->getRepository(Producer::class)->find($producerId);

        if (!isset($producer)) {
            throw new BadRequsetException('Such producer does not exist');
        }

        $typeId = $this->request->request->get('typeId');
        $type = $entityManager->getRepository(Type::class)->find($typeId);

        if (!isset($type)) {
            throw new BadRequsetException('Such type does not exist');
        }

        $image = $this->request->files->get('image');

        try {
            $imagePath = $this->uploader->upload($image);
        } catch (FileException $e) {
            throw new ServerErrorException($e->getMessage());
        }

        $product = new Product();
        $product->setTitle($this->request->request->get('title'));
        $product->setDescription($this->request->request->get('description'));
        $product->setCompound($this->request->request->get('compound'));
        $product->setStorageConditions($this->request->request->get('storageConditions'));
        $product->setWeight($this->request->request->get('weight'));
        $product->setImage($imagePath);
        $product->setType($type);
        $product->setProducer($producer);

        $errors = $this->validator->validate($product);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($product);

        if (isset($user) && in_array(Role::ROLE_VENDOR->value, $user->getRoles())) {
            $userPhone = $user->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

            $vendorProduct = new VendorProduct();
            $vendorProduct->setVendor($user->getVendor());
            $vendorProduct->setProduct($product);
            $vendorProduct->setPrice($this->request->request->get('price'));

            if ($this->request->request->has('quantity')) {
                $vendorProduct->setQuantity($this->request->request->get('quantity'));
            }

            $errors = $this->validator->validate($vendorProduct);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                throw new BadRequsetException($errorsString);
            }

            $entityManager->persist($vendorProduct);
        }

        $entityManager->flush();

        return $product->getId();
    }

    /**
     * Summary of list
     * @return array
     */
    public function list(): array
    {
        $querryBuilder = $this->productRepository->searchByTitle($this->request);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $this->request->query->getInt('page', 1),
            $this->request->query->get('limit', 5)
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
     * @return array
     */
    public function getProductsVendorDoesNotSell(): array
    {
        $entityMnager = $this->registry->getManager();

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityMnager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $vendorId = $user->getVendor()->getId();
        $querryBuilder = $this->productRepository->searchByTitle($this->request, $vendorId);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $this->request->query->getInt('page', 1),
            $this->request->query->get('limit', 5),
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
