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
use App\Constants\OrderConstatns;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Constants\RoleConstants;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\OrderRepository;

#[Route('/api/order', name: 'api_order_')]
class OrderController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
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

        $deliveryDate = DateTime::createFromFormat('Y-m-d\TH:i', $decoded->deliveryTime);

        $order = new Order();
        $order->setCustomer($user);
        $order->setPaymentMethod($decoded->paymentMethod);
        $order->setDeliveryTime($deliveryDate);
        $order->setOrderStatus(OrderConstatns::ORDER_PROCESSED);

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

    #[Route('/current', name: 'get_orders_of_current_user', methods: 'get')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
    public function getUserOrders(
        PaginatorInterface $paginator,
        OrderRepository $orderRepository,
        Request $request,
        Security $security,
        ManagerRegistry $registry,
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $querryBuilder = $orderRepository->getAllOrdersBelonignToUser($user);

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
            context: [AbstractNormalizer::GROUPS => ['orders']]
        );
    }

    #[Route(name: 'get_all_orders', methods: 'get')]
    #[IsGranted(RoleConstants::ROLE_ADMIN, message: 'You are not allowed to access this route.')]
    public function getAllOrders(
        PaginatorInterface $paginator,
        OrderRepository $orderRepository,
        Request $request,
    ): JsonResponse {
        $querryBuilder = $orderRepository->createQuerryBuilderForPagination();

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
            context: [AbstractNormalizer::GROUPS => ['orders', 'orders_admin']]
        );
    }

    #[Route('/{id<\d+>}', name: 'patch_order', methods: 'patch')]
    #[IsGranted(RoleConstants::ROLE_ADMIN, message: 'You are not allowed to access this route.')]
    public function patchOrder(
        int $id,
        ManagerRegistry $registry,
        Request $request,
        OrderValidator $orderValidator,
        ValidatorInterface $validator
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $decoded = json_decode($request->getContent());

        if (!$orderValidator->isValidToPatchOrder($decoded)) {
            return $this->json(['message' => 'Insufucient data'], 400);
        }

        $order = $entityManager->getRepository(Order::class)->find($id);

        if (!isset($order)) {
            return $this->json(['message' => 'Such order does not exist'], 404);
        }

        $deliveryDate = DateTime::createFromFormat('Y-m-d\TH:i', $decoded->deliveryTime);

        $order->setPaymentMethod($decoded->paymentMethod);
        $order->setOrderStatus($decoded->orderStatus);
        $order->setDeliveryTime($deliveryDate);

        $errors = $validator->validate($order);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->json(['message' => 'Succesfully patched'], 200);
    }

    #[Route('/customer/{id<\d+>}', name: 'cancel_order', methods: 'patch')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
    public function cancelOrder(
        int $id,
        ManagerRegistry $registry,
        ValidatorInterface $validator,
        Security $security,
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $order = $entityManager->getRepository(Order::class)->find($id);

        if (!isset($order)) {
            return $this->json(['message' => 'Such order does not exist'], 404);
        }

        if ($security->getUser()->getUserIdentifier() !== $order->getCustomer()->getUserIdentifier()) {
            return $this->json(['message' => 'You can not cancel this order']);
        }

        $order->setOrderStatus(OrderConstatns::ORDER_CANCELED);

        $errors = $validator->validate($order);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->json(['message' => 'Succesfully patched'], 200);
    }
}
