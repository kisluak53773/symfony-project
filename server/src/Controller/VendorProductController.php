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

#[Route('/api/vendorProduct', name: 'api_vendorProduct_')]
class VendorProductController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    public function add(Request $request, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->vendorId) || !isset($decoded->productId) || !isset($decoded->price)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $vendorId = $decoded->vendorId;
        $vendor = $entityManager->getRepository(Vendor::class)->find($vendorId);

        if (!isset($vendor)) {
            return $this->json(['message' => 'such vendor does not exist'], 404);
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

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
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
