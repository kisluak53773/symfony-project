<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Vendor;
use App\Entity\Product;
use App\Entity\VendorProduct;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\VendorProductRepository;
use App\Services\Validator\VendorProductValidator;
use App\Constants\RoleConstants;

#[Route('/api/vendorProduct', name: 'api_vendorProduct_')]
class VendorProductController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    #[IsGranted('ROLE_VENDOR', message: 'You are not allowed to access this route.')]
    public function add(
        Request $request,
        ManagerRegistry $managerRegistry,
        Security $security
    ): JsonResponse {
        $entityManager = $managerRegistry->getManager();
        $decoded = json_decode($request->getContent());
        $user = $security->getUser();

        if (!isset($decoded->productId) || !isset($decoded->price)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        if (isset($user) && in_array(RoleConstants::ROLE_VENDOR, $user->getRoles())) {
            $userPhone = $user->getUserIdentifier();
            $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
            $vendor = $user->getVendor();
        } else {
            if (!isset($decoded->vendorId)) {
                return $this->json(['message' => 'insufficient data'], 400);
            }

            $vendorId = $decoded->vendorId;
            $vendor = $entityManager->getRepository(Vendor::class)->find($vendorId);

            if (!isset($vendor)) {
                return $this->json(['message' => 'such vendor does not exist'], 404);
            }
        }

        $productId = $decoded->productId;
        $product = $entityManager->getRepository(Product::class)->find($productId);

        if (!isset($product)) {
            return $this->json(['message' => 'such product does not exist'], 404);
        }

        $vendorProduct = new VendorProduct();
        $vendorProduct->setPrice($decoded->price);
        $vendorProduct->setVendor($vendor);
        $vendorProduct->setProduct($product);

        if (isset($decoded->quantity)) {
            $vendorProduct->setQuantity($decoded->quantity);
        }

        $entityManager->persist($vendorProduct);
        $entityManager->flush();


        return $this->json(['message' => 'vendor now sells this product', 'id' => $vendorProduct->getId()], 201);
    }

    #[Route('/vendor', name: 'get_for_vendor', methods: 'get')]
    #[IsGranted('ROLE_VENDOR', message: 'You are not allowed to access this route.')]
    public function get(
        ManagerRegistry $registry,
        Security $security,
        Request $request,
        PaginatorInterface $paginator,
        VendorProductRepository $vendorProductRepository
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $user = $security->getUser();

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $user->getUserIdentifier()]);
        $vendor = $user->getVendor();

        $querryBuilder = $vendorProductRepository->createQueryBuilderForPaginationWithVendor($vendor);

        $pagination = $paginator->paginate(
            $querryBuilder,
            $request->query->getInt('page', 1),
            $request->query->get('limit', 5)
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

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['vendor_products']]
        );
    }

    #[Route('/vendor/update/{id<\d+>}', name: 'update_for_vendor', methods: 'patch')]
    #[IsGranted('ROLE_VENDOR', message: 'You are not allowed to access this route.')]
    public function patchVendorProdut(
        int $id,
        Request $request,
        ManagerRegistry $registry,
        VendorProductValidator $validator
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $decoded = json_decode($request->getContent());

        if (!$validator->validateVendorToPatch($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($id);
        $vendorProduct->setPrice($decoded->price);
        $vendorProduct->setQuantity($decoded->quantity);

        $entityManager->persist($vendorProduct);
        $entityManager->flush();

        return $this->json(['message' => 'Updated successfully'], 200);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted('ROLE_VENDOR', message: 'You are not allowed to access this route.')]
    public function delete(int $id, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($id);

        if (!isset($vendorProduct)) {
            return $this->json(['message' => 'no such vendor product exist'], 404);
        }

        $entityManager->remove($vendorProduct);
        $entityManager->flush();

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
