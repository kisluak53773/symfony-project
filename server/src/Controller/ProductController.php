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
use App\Entity\Vendor;
use App\Entity\Type;
use App\Entity\VendorProduct;
use App\Services\Uploader\ProductImageUploader;
use App\Services\Validator\ProductValidator;

#[Route('/api/product', name: 'api_product_')]
class ProductController extends AbstractController
{
    #[Route('/vendor', name: 'add', methods: 'post')]
    public function addWithVendor(
        Request $request,
        ManagerRegistry $registry,
        ProductImageUploader $uploader,
        ProductValidator $validator
    ): JsonResponse {
        $entityManager = $registry->getManager();

        if (!$validator->isProductWithVendorValid($request)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $producerId = $request->request->get('producerId');
        $producer = $entityManager->getRepository(Producer::class)->find($producerId);

        if (!isset($producer)) {
            return $this->json(['message' => 'such producer does not exist'], 400);
        }

        $vendorId = $request->request->get('vendorId');
        $vendor = $entityManager->getRepository(Vendor::class)->find($vendorId);

        if (!isset($vendor)) {
            return $this->json(['message' => 'such vendor does not exist'], 400);
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
        $entityManager->persist($product);

        $vendorProduct = new VendorProduct();
        $vendorProduct->setPrice($request->request->get('price'));
        $vendorProduct->setProduct($product);
        $vendorProduct->setVendor($vendor);
        $entityManager->persist($vendorProduct);

        $entityManager->flush();

        return $this->json(['message' => 'new produt craeted'], 201);
    }

    #[Route(name: 'list', methods: ['GET'])]
    public function list(Request $request, PaginatorInterface $paginator, ProductRepository $productRepository): JsonResponse
    {
        $querryBuilder = $productRepository->createQueryBuilderForPagination();

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

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
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
