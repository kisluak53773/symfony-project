<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Producer;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Entity\Type;
use App\Entity\VendorProduct;
use App\Services\Uploader\ProductImageUploader;
use App\Services\Validator\ProductValidator;
use Symfony\Bundle\SecurityBundle\Security;
use App\Enum\Role;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/product', name: 'api_product_')]
class ProductController extends AbstractController
{
    #[Route('/create', name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function addWithVendor(
        Request $request,
        ManagerRegistry $registry,
        ProductImageUploader $uploader,
        ProductValidator $productValidator,
        Security $security,
        ValidatorInterface $validator
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $user = $security->getUser();

        if (!$productValidator->isProductWithVendorValid($request)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $producerId = $request->request->get('producerId');
        $producer = $entityManager->getRepository(Producer::class)->find($producerId);

        if (!isset($producer)) {
            return $this->json(['message' => 'such producer does not exist'], 400);
        }

        $typeId = $request->request->get('typeId');
        $type = $entityManager->getRepository(Type::class)->find($typeId);

        if (!isset($type)) {
            return $this->json(['message' => 'such type does not exist'], 400);
        }

        $image = $request->files->get('image');

        try {
            $imagePath = $uploader->upload($image);
        } catch (FileException $e) {
            return $this->json(['message' => $e->getMessage()], 500);
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

        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(['message' => 'Validation failed', 'errors' => $errorMessages], 400);
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

            $errors = $validator->validate($vendorProduct);

            if (count($errors) > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }

                return $this->json(['message' => 'Validation failed', 'errors' => $errorMessages], 400);
            }

            $entityManager->persist($vendorProduct);
        }

        $entityManager->flush();

        return $this->json(['message' => 'new produt craeted', 'id' => $product->getId()], 201);
    }

    #[Route(name: 'list', methods: ['GET'])]
    public function list(
        Request $request,
        PaginatorInterface $paginator,
        ProductRepository $productRepository
    ): JsonResponse {
        $querryBuilder = $productRepository->searchByTitle($request);

        $pagination = $paginator->paginate(
            $querryBuilder,
            $request->query->getInt('page', 1),
            $request->query->get('limit', 5)
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

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['product_list']]
        );
    }

    #[Route('/vendor', name: 'get_products_vendor_does_not_sell', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getProductsVendorDoesNotSell(
        Request $request,
        PaginatorInterface $paginator,
        ProductRepository $productRepository,
        Security $security,
        ManagerRegistry $registry
    ): JsonResponse {
        $entityMnager = $registry->getManager();

        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityMnager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $vendorId = $user->getVendor()->getId();
        $querryBuilder = $productRepository->searchByTitle($request, $vendorId);

        $pagination = $paginator->paginate(
            $querryBuilder,
            $request->query->getInt('page', 1),
            $request->query->get('limit', 5),
        );

        $products = $pagination->getItems();
        $totalItems = $pagination->getTotalItemCount();
        $itemsPerPage = $pagination->getItemNumberPerPage();
        $currentPage = $pagination->getCurrentPageNumber();
        $totalPages = ceil($totalItems / $itemsPerPage);

        if (!isset($products)) {
            return $this->json(['message' => 'there are no such proucts'], 404);
        }

        $response = [
            'total_items' => $totalItems,
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'data' => $products,
        ];

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['vendor_does_not_sell']]
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted(Role::ROLE_ADMIN->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!isset($product)) {
            return $this->json(['message' => 'no such product exist'], 404);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
