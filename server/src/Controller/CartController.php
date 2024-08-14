<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Services\Exception\Request\RequestException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\DTO\Cart\AddToCartDto;
use App\DTO\Cart\IncreaseDto;
use App\DTO\Cart\DecreaseDto;
use App\Contract\Service\CartServiceInterface;

#[Route('/api/cart', name: 'api_cart_')]
class CartController extends AbstractController
{
    public function __construct(private CartServiceInterface $cartService) {}

    #[Route(name: 'create', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function createCart(): JsonResponse
    {
        try {
            $this->cartService->createCart();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'cart created'], 201);
    }

    #[Route('/prodcuts', name: 'get_products', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getProductsCart(): JsonResponse
    {
        try {
            $cartProducts = $this->cartService->getProductsCart();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(
            data: $cartProducts,
            context: [AbstractNormalizer::GROUPS => ['cart_product']]
        );
    }

    #[Route('/add', name: 'addToCart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function addToCart(#[MapRequestPayload] AddToCartDto $addToCartDto): JsonResponse
    {
        try {
            $response = $this->cartService->add($addToCartDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json($response['responseMessage'], $response['statucCode']);
    }

    #[Route('/increase', name: 'increase_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function increaseProductAmount(#[MapRequestPayload] IncreaseDto $increaseDto): JsonResponse
    {
        try {
            $quantity = $this->cartService->increase($increaseDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Quantity increased', 'quantity' => $quantity], 200);
    }

    #[Route('/decrease', name: 'decrease_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function decreaseProductAmount(#[MapRequestPayload] DecreaseDto $decreaseDto): JsonResponse
    {
        try {
            $this->cartService->decrease($decreaseDto);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Decreased successfully'], 200);
    }

    #[Route('/remove/{vendorProductId}', name: 'remove_from_cart', methods: 'delete', requirements: ['vendorProductId' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function removeFromCart(int $vendorProductId): JsonResponse
    {
        try {
            $this->cartService->removeFromCart($vendorProductId);
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Deleted sucseffully'], 200);
    }

    #[Route('/removeAll', name: 'remove_all_from_cart', methods: 'delete')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function removeAllFromCart(): JsonResponse
    {
        try {
            $this->cartService->removeAllFromCart();
        } catch (RequestException $e) {
            return $this->json(['message' => $e->getMessage()], $e->getStatsCode());
        }

        return $this->json(['message' => 'Deleted sucseffully'], 200);
    }
}
