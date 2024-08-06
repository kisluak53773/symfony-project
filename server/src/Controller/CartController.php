<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use App\Entity\Cart;
use App\Services\Validator\CartValidator;
use App\Entity\CartProduct;
use App\Entity\VendorProduct;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    #[Route(name: 'create', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
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

    #[Route('/prodcuts', name: 'get_products', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getProductsCart(
        ManagerRegistry $registry,
        Security $security
    ): JsonResponse {
        $entityManager = $registry->getManager();

        $userPhoen = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhoen]);

        $cartProducts = $user->getCart()->getCartProducts();

        return $this->json(
            data: $cartProducts,
            context: [AbstractNormalizer::GROUPS => ['cart_product']]
        );
    }

    #[Route('/add', name: 'addToCart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
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
        $qunatity = $vendorProduct->getQuantity() > $decoded->quantity ?
            $decoded->quantity : $vendorProduct->getQuantity();

        if (!isset($vendorProduct) || $vendorProduct->getQuantity() === 0) {
            return $this->json(['message' => 'No such item in stock'], 400);
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (isset($cartProduct)) {
            $cartProduct->increaseQuantity($qunatity);
            $vendorProduct->decreaseQuantity($qunatity);

            $entityManager->persist($cartProduct);
            $entityManager->persist($vendorProduct);
            $entityManager->flush();

            return $this->json(['message' => 'Quantity increased'], 200);
        }


        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $cart = $user->getCart();

        $cartProduct = new CartProduct();
        $cartProduct->setCart($cart);
        $cartProduct->setVendorProduct($vendorProduct);
        $cartProduct->setQuantity($qunatity);

        $errors = $validator->validate($cartProduct);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $vendorProduct->decreaseQuantity($qunatity);

        $entityManager->persist($cartProduct);
        $entityManager->persist($vendorProduct);
        $entityManager->flush();

        return $this->json(['message' => 'Product added to cart', 'id' => $cartProduct->getId()], 201);
    }

    #[Route('/increase', name: 'increase_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
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
            return $this->json(['message' => 'No such item in stock'], 400);
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            return $this->json(['message' => 'No such product in cart'], 400);
        }

        $quantity = $vendorProduct->getQuantity() > $decoded->quantity ?
            $decoded->quantity : $vendorProduct->getQuantity();

        $cartProduct->increaseQuantity($quantity);
        $vendorProduct->decreaseQuantity($quantity);

        $entityManager->persist($cartProduct);
        $entityManager->persist($vendorProduct);
        $entityManager->flush();

        return $this->json(['message' => 'Quantity increased', 'quantity' => $quantity], 200);
    }

    #[Route('/decrease', name: 'decrease_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function decreaseProductAmount(
        ManagerRegistry $managerRegistry,
        Request $request,
        CartValidator $cartValidator
    ): JsonResponse {
        $entityManager = $managerRegistry->getManager();
        $decoded = json_decode($request->getContent());

        if (!$cartValidator->isValidToDecreaseAmounInCart($decoded)) {
            return $this->json(['message' => 'Insufficient data'], 400);
        }

        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($decoded->vendorProductId);

        if (!isset($vendorProduct)) {
            return $this->json(['message' => 'This vendor does not sell this item'], 400);
        }

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

        return $this->json(['message' => 'Decreased successfully'], 200);
    }

    #[Route('/remove/{vendorProductId<\d+>}', name: 'remove_from_cart', methods: 'delete')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function removeFromCart(int $vendorProductId, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();
        $vendorProduct = $entityManager->getRepository(VendorProduct::class)->find($vendorProductId);

        if (!isset($vendorProduct)) {
            return $this->json(['message' => 'Vendor does not sell this product'], 404);
        }

        $cartProduct = $entityManager->getRepository(CartProduct::class)->findOneBy(['vendorProduct' => $vendorProduct]);

        if (!isset($cartProduct)) {
            return $this->json(['message' => 'No such product in cart'], 404);
        }

        $vendorProduct->increaseQuantity($cartProduct->getQuantity());

        $entityManager->persist($vendorProduct);
        $entityManager->remove($cartProduct);
        $entityManager->flush();

        return $this->json(['message' => 'Deleted sucseffully'], 200);
    }

    #[Route('/removeAll', name: 'remove_all_from_cart', methods: 'delete')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function removeAllFromCart(ManagerRegistry $registry, Security $security): JsonResponse
    {
        $entityManager = $registry->getManager();
        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $cartProducts = $user->getCart()->getCartProducts()->getValues();

        if (count($cartProducts) === 0) {
            return $this->json(['message' => 'Your cart is empty'], 400);
        }

        foreach ($cartProducts as $cartProduct) {
            $entityManager->remove($cartProduct);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Deleted sucseffully'], 200);
    }
}
