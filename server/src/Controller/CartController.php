<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Enum\Role;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\DTO\Cart\AddToCartDto;
use App\DTO\Cart\IncreaseDto;
use App\DTO\Cart\DecreaseDto;
use App\Contract\Service\CartServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

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
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'cart created'], Response::HTTP_CREATED);
    }

    #[Route('/prodcuts', name: 'get_products', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getProductsCart(): JsonResponse
    {
        try {
            $cartProducts = $this->cartService->getProductsCart();
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
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
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json($response['responseMessage'], $response['statusCode']);
    }

    #[Route('/increase', name: 'increase_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function increaseProductAmount(#[MapRequestPayload] IncreaseDto $increaseDto): JsonResponse
    {
        try {
            $quantity = $this->cartService->increase($increaseDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Quantity increased', 'quantity' => $quantity], Response::HTTP_OK);
    }

    #[Route('/decrease', name: 'decrease_amount_of_product_in_cart', methods: 'post')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function decreaseProductAmount(#[MapRequestPayload] DecreaseDto $decreaseDto): JsonResponse
    {
        try {
            $this->cartService->decrease($decreaseDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Decreased successfully'], Response::HTTP_OK);
    }

    #[Route('/remove/{vendorProductId}', name: 'remove_from_cart', methods: 'delete', requirements: ['vendorProductId' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function removeFromCart(int $vendorProductId): JsonResponse
    {
        try {
            $this->cartService->removeFromCart($vendorProductId);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Deleted sucseffully'], Response::HTTP_OK);
    }

    #[Route('/removeAll', name: 'remove_all_from_cart', methods: 'delete')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function removeAllFromCart(): JsonResponse
    {
        try {
            $this->cartService->removeAllFromCart();
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Deleted sucseffully'], Response::HTTP_OK);
    }
}
