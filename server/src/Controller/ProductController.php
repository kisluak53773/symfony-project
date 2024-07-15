<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use App\Services\Product\FileUploader;
use App\Services\Product\ProductValidator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Entity\Producer;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/product', name: 'api_product_')]
class ProductController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    public function add(Request $request, ManagerRegistry $registry, FileUploader $uploader, ProductValidator $validator): JsonResponse
    {
        $entityManager = $registry->getManager();

        if (!$validator->validateForCreation($request)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $image = $request->files->get('image');

        try {
            $imagePath = $uploader->upload($image);
        } catch (FileException $e) {
            return $this->json(['message' => $e->getMessage()], 500);
        }

        $producerId = $request->request->get('producerId');
        $producer = $entityManager->getRepository(Producer::class)->find($producerId);

        if (!$producer) {
            return $this->json(['message' => 'such producer does not exist'], 400);
        }

        $product = new Product();
        $product->setTitle($request->request->get('title'));
        $product->setDescription($request->request->get('description'));
        $product->setCompound($request->request->get('compound'));
        $product->setStorageConditions($request->request->get('storageConditions'));
        $product->setType($request->request->get('type'));
        $product->setWeight($request->request->get('weight'));
        $product->setPrice($request->request->get('price'));
        $product->setProducer($producer);
        $product->setImage($imagePath);

        $entityManager->persist($product);
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

        return $this->json($response, 200, [], [
            AbstractNormalizer::GROUPS => ['product_list']
        ]);
    }
}
