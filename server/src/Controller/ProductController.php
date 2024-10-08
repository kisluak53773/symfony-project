<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\DTO\Product\CreateProductDto;
use App\DTO\Product\ProductSearchParamsDto;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Contract\Service\ProductServiceIntrrafce;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

#[Route('/api/product', name: 'api_product_')]
class ProductController extends AbstractController
{
    public function __construct(private ProductServiceIntrrafce $productService) {}

    #[Route('/create', name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function addWithVendor(
        #[MapRequestPayload] CreateProductDto $createProductDto,
        #[MapUploadedFile([
            new Assert\File(mimeTypes: ['image/png', 'image/jpeg', 'image/webp']),
            new Assert\Image(maxWidth: 3840, maxHeight: 2160),
        ])]
        UploadedFile $image
    ): JsonResponse {
        try {
            $id = $this->productService->addWithVendor($image, $createProductDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'new produt craeted', 'id' => $id], Response::HTTP_CREATED);
    }

    #[Route(name: 'list', methods: ['GET'])]
    public function list(
        #[MapQueryString] ProductSearchParamsDto $productSearchParamsDto = new ProductSearchParamsDto()
    ): JsonResponse {
        try {
            $response = $this->productService->list($productSearchParamsDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['product_list']]
        );
    }

    #[Route('/vendor', name: 'get_products_vendor_does_not_sell', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getProductsVendorDoesNotSell(
        #[MapQueryString] ProductSearchParamsDto $productSearchParamsDto  = new ProductSearchParamsDto()
    ): JsonResponse {
        try {
            $response = $this->productService->getProductsVendorDoesNotSell($productSearchParamsDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['vendor_does_not_sell']]
        );
    }

    #[Route('/{id}', name: 'delete', methods: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_ADMIN->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->productService->delete($id);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'deleted sucseffully'], Response::HTTP_OK);
    }
}
