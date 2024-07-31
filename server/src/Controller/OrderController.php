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
use App\Entity\Order;
use DateTime;

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
        $decoded = json_decode($request->getContent());

        if (!$orderValidator->isValidToCreateOrder($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $deliveryDate = DateTime::createFromFormat('Y-m-d\TH:i:s', $decoded->deliveryTime);

        if ($deliveryDate === false) {
            return $this->json(['message' => 'Invalid delivery time format'], 400);
        }

        $order = new Order();
        $order->setCustomer($user);
        $order->setPaymentMethod($decoded->paymentMethod);
        $order->setDeliveryTime($deliveryDate);

        if (isset($decoded->comment)) {
            $order->setComment($decoded->comment);
        }

        $errors = $validator->validate($order);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($order);

        $cart = $user->getCart();

        if (!isset($cart)) {
            return $this->json(['message' => 'You do not have a cart'], 400);
        }

        $cartProducts = $cart->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            return $this->json(['message' => 'Your cart is empty'], 400);
        }

        foreach ($cartProducts as $cartProduct) {
            $orderProduct = new OrderProduct();
            $orderProduct->setOrderEntity($order);
            $orderProduct->setQuantity($cartProduct->getQuantity());
            $orderProduct->setVendorProduct($cartProduct->getVendorProduct());

            $errors = $validator->validate($orderProduct);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return $this->json(['message' => $errorsString], 400);
            }

            $entityManager->persist($orderProduct);
            $entityManager->remove($cartProduct);
        }

        $entityManager->flush();

        return $this->json(['message' => 'order created'], 200);
    }
}
