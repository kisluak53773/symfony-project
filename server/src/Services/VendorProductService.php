<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\VendorProductRepository;
use App\Services\Validator\VendorProductValidator;
use App\Enum\Role;
use App\Entity\User;
use App\Entity\VendorProduct;
use App\Entity\Product;
use App\Entity\Vendor;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;

class VendorProductService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Security $security,
        private ValidatorInterface $validator,
        private PaginatorInterface $paginator,
        private VendorProductRepository $vendorProductRepository,
        private VendorProductValidator $vendorProductValidator
    ) {
    }

    /**
     * Summary of add
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return int
     */
    public function add(Request $request): int
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($request->getContent());
        $user = $this->security->getUser();

        if (!isset($decoded->productId) || !isset($decoded->price)) {
            throw new BadRequsetException();
        }

        if (isset($user) && in_array(Role::ROLE_VENDOR->value, $user->getRoles())) {
            $userPhone = $user->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
            $vendor = $user->getVendor();
        } else {
            if (!isset($decoded->vendorId)) {
                throw new BadRequsetException();
            }

            $vendorId = $decoded->vendorId;
            $vendor = $entityManager->getRepository(Vendor::class)->find($vendorId);

            if (!isset($vendor)) {
                throw new NotFoundException('Such vendor does not exist');
            }
        }

        $productId = $decoded->productId;
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!isset($product)) {
            throw new NotFoundException('Such product does not exist');
        }

        $vendorProduct = new VendorProduct();
        $vendorProduct->setPrice($decoded->price);
        $vendorProduct->setVendor($vendor);
        $vendorProduct->setProduct($product);

        if (isset($decoded->quantity)) {
            $vendorProduct->setQuantity($decoded->quantity);
        }

        $errors = $this->validator->validate($vendorProduct);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($vendorProduct);
        $entityManager->flush();


        return $vendorProduct->getId();
    }

    /**
     * Summary of get
     * @param \Symfony\Component\HttpFoundation\Request $request
     * 
     * @return array
     */
    public function get(Request $request): array
    {
        $entityManager = $this->registry->getManager();
        $user = $this->security->getUser();

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $user->getUserIdentifier()]);
        $vendor = $user->getVendor();

        $querryBuilder = $this->vendorProductRepository->createQueryBuilderForPaginationWithVendor($vendor);

        $pagination = $this->paginator->paginate(
            $querryBuilder,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 5)
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
    public function patchVendorProdut(int $id, Request $request): void
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($request->getContent());

        $this->vendorProductValidator->validateVendorToPatch($decoded);

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($id);
        $vendorProduct->setPrice($decoded->price);
        $vendorProduct->setQuantity($decoded->quantity);

        $errors = $this->validator->validate($vendorProduct);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($vendorProduct);
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

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($id);

        if (!isset($vendorProduct)) {
            throw new NotFoundException('Vendor does not sell this product');
        }

        $entityManager->remove($vendorProduct);
        $entityManager->flush();
    }
}
