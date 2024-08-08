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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\ServerErrorException
     * 
     * @return int
     */
    public function addWithVendor(Request $request): int
    {
        $entityManager = $this->registry->getManager();
        $user = $this->security->getUser();

        $this->productValidator->isProductWithVendorValid($request);

        $producerId = $request->request->get('producerId');
        $producer = $entityManager->getRepository(Producer::class)->find($producerId);

        if (!isset($producer)) {
            throw new BadRequsetException('Such producer does not exist');
        }

        $typeId = $request->request->get('typeId');
        $type = $entityManager->getRepository(Type::class)->find($typeId);

        if (!isset($type)) {
            throw new BadRequsetException('Such type does not exist');
        }

        $image = $request->files->get('image');

        try {
            $imagePath = $this->uploader->upload($image);
        } catch (FileException $e) {
            throw new ServerErrorException($e->getMessage());
        }

        $product = new Product();
        $product->setTitle($request->request->get('title'));
        $product->setDescription($request->request->get('description'));
        $product->setCompound($request->request->get('compound'));
        $product->setStorageConditions($request->request->get('storageConditions'));
        $product->setWeight($request->request->get('weight'));
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
            $vendorProduct->setPrice($request->request->get('price'));

            if ($request->request->has('quantity')) {
                $vendorProduct->setQuantity($request->request->get('quantity'));
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
     */
    public function list(Request $request): array
    {
        $querryBuilder = $this->productRepository->searchByTitle($request);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
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
    public function getProductsVendorDoesNotSell(Request $request): array
    {
        $entityMnager = $this->registry->getManager();

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityMnager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $vendorId = $user->getVendor()->getId();
        $querryBuilder = $this->productRepository->searchByTitle($request, $vendorId);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5),
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
