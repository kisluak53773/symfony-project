<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Services\Validator\OrderValidator;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Entity\OrderProduct;
use App\Entity\VendorProduct;
use App\Entity\Order;

#[Route('/api/order', name: 'api_order_')]
class OrderController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    public function index(
        ManagerRegistry $registry,
        Security $security,
        Request $request,
        OrderValidator $orderValidator,
        ValidatorInterface $validator
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $decoded = json_decode($request->getContent(), true);

        if (!$orderValidator->isValidToCreateOrder($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $order = new Order();
        $order->setCustomer($user);
        $order->setPaymentMethod($decoded['paymentMethod']);

        $errors = $validator->validate($order);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($order);

        foreach ($decoded['products'] as $product) {
            $vendorProdcut = $entityManager->getRepository(VendorProduct::class)->find($product['vendorProductId']);

            if (!isset($vendorProdcut)) {
                return $this->json(['message' => 'Vendor does not sell this product'], 400);
            }

            $orderProduct = new OrderProduct();
            $orderProduct->setOrderEntity($order);
            $orderProduct->setQuantity($product['qunatity']);
            $orderProduct->setVendorProduct($vendorProdcut);

            $errors = $validator->validate($orderProduct);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return $this->json(['message' => $errorsString], 400);
            }

            $entityManager->persist($orderProduct);
        }

        $entityManager->flush();

        return $this->json(['message' => 'order created'], 200);
    }
}
