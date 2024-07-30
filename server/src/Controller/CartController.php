<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Constants\RoleConstants;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Entity\Cart;
use App\Services\Validator\CartValidator;
use App\Entity\CartProduct;
use App\Entity\VendorProduct;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    #[Route(name: 'create', methods: 'post')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
    public function createCart(
        ManagerRegistry $registry,
        Security $security
    ): JsonResponse {
        $entityManager = $registry->getManager();

        $userPhoen = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhoen]);

        $cart = new Cart();
        $cart->setCustomer($user);

        $entityManager->persist($cart);
        $entityManager->flush();

        return $this->json(['message' => 'cart created'], 201);
    }

    #[Route('/add', name: 'addToCart', methods: 'post')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
    public function addToCart(
        ManagerRegistry $registry,
        Security $security,
        Request $request,
        ValidatorInterface $validator,
        CartValidator $cartValidator
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $decoded = json_decode($request->getContent());

        if (!$cartValidator->isValidToAddToCart($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($decoded->vendorProductId);

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            $this->json(['message' => 'No such item in stock'], 400);
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (isset($cartProduct)) {
            if ($vendorProduct->getQuantity() > $decoded->quantity) {
                $cartProduct->increaseQuantity($decoded->quantity);
                $vendorProduct->decreaseQuantity($decoded->quantity);
            } else {
                $cartProduct->increaseQuantity($vendorProduct->getQuantity());
                $vendorProduct->decreaseQuantity($vendorProduct->getQuantity());
            }

            $entityManager->persist($cartProduct);
            $entityManager->persist($vendorProduct);
            $entityManager->flush();

            return $this->json(['message' => 'quantity increased'], 200);
        }


        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $cart = $user->getCart();

        $cartProduct = new CartProduct();
        $cartProduct->setCart($cart);
        $cartProduct->setVendorProduct($vendorProduct);
        $cartProduct->setQuantity($decoded->quantity);

        $errors = $validator->validate($cartProduct);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $vendorProduct->decreaseQuantity(1);

        $entityManager->persist($cartProduct);
        $entityManager->persist($vendorProduct);
        $entityManager->flush();

        return $this->json(['message' => 'cart created'], 201);
    }

    #[Route('/increase', name: 'increase_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
    public function increaseProductAmount(
        ManagerRegistry $managerRegistry,
        Request $request,
        CartValidator $cartValidator
    ): JsonResponse {
        $entityManager = $managerRegistry->getManager();
        $decoded = json_decode($request->getContent());

        if (!$cartValidator->isValidToDecreaseAmounInCart($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($decoded->vendorProductId);

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            $this->json(['message' => 'No such item in stock'], 400);
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            return $this->json(['message' => 'No such product in cart'], 400);
        }

        if ($vendorProduct->getQuantity() > $decoded->quantity) {
            $cartProduct->increaseQuantity($decoded->quantity);
            $vendorProduct->decreaseQuantity($decoded->quantity);
        } else {
            $cartProduct->increaseQuantity($vendorProduct->getQuantity());
            $vendorProduct->decreaseQuantity($vendorProduct->getQuantity());
        }

        $entityManager->persist($cartProduct);
        $entityManager->persist($vendorProduct);
        $entityManager->flush();

        return $this->json(['message' => 'quantity increased'], 200);
    }

    #[Route('/decrease', name: 'decrease_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
    public function decreaseProductAmount(
        ManagerRegistry $managerRegistry,
        Request $request,
        CartValidator $cartValidator
    ): JsonResponse {
        $entityManager = $managerRegistry->getManager();
        $decoded = json_decode($request->getContent());

        if (!$cartValidator->isValidToDecreaseAmounInCart($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($decoded->vendorProductId);
        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            return $this->json(['message' => 'No such product in cart'], 400);
        }

        if ($cartProduct->getQuantity() > $decoded->quantity) {
            $cartProduct->decreaseQuantity($decoded->quantity);
            $vendorProduct->increaseQuantity($decoded->quantity);

            $entityManager->persist($cartProduct);
            $entityManager->persist($vendorProduct);
        } else {
            $vendorProduct->increaseQuantity($cartProduct->getQuantity());

            $entityManager->persist($vendorProduct);
            $entityManager->remove($cartProduct);
        }

        $entityManager->flush();

        return $this->json(['message' => 'increased successfully'], 200);
    }

    #[Route('/remove/{vendorProductId<\d+>}', name: 'remove_from_cart', methods: 'delete')]
    #[IsGranted(RoleConstants::ROLE_USER, message: 'You are not allowed to access this route.')]
    public function removeFromCart(int $vendorProductId, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();
        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($vendorProductId);

        if (!isset($cartProduct)) {
            return $this->json(['message' => 'Vendor does not sell this product'], 404);
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            return $this->json(['message' => 'no such product in cart'], 404);
        }

        $entityManager->remove($cartProduct);
        $entityManager->flush();

        return $this->json(['message' => 'deleted sucseffully', 'vendorProductId' => $vendorProductId], 200);
    }
}
