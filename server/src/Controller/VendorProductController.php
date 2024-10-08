<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use App\Enum\Role;
use App\DTO\VendorProduct\CreateVendorProductDto;
use App\DTO\VendorProduct\PatchVendorProductDto;
use App\DTO\PaginationQueryDto;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use App\Contract\Service\VendorProductServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

#[Route('/api/vendorProduct', name: 'api_vendorProduct_')]
class VendorProductController extends AbstractController
{
    public function __construct(private VendorProductServiceInterface $vendorProductService) {}

    #[Route(name: 'add', methods: 'post')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function add(#[MapRequestPayload] CreateVendorProductDto $createVendorProductDto): JsonResponse
    {
        try {
            $id = $this->vendorProductService->add($createVendorProductDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }


        return $this->json(['message' => 'vendor now sells this product', 'id' => $id], Response::HTTP_OK);
    }

    #[Route('/vendor', name: 'get_for_vendor', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function get(
        #[MapQueryString] PaginationQueryDto $paginationQueryDto = new PaginationQueryDto()
    ): JsonResponse {
        try {
            $response = $this->vendorProductService->get($paginationQueryDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(
            data: $response,
            context: [AbstractNormalizer::GROUPS => ['vendor_products']]
        );
    }

    #[Route('/vendor/update/{id}', name: 'update_for_vendor', methods: 'patch', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function patchVendorProdut(
        int $id,
        #[MapRequestPayload] PatchVendorProductDto $patchVendorProduct
    ): JsonResponse {
        try {
            $this->vendorProductService->patchVendorProdut($id, $patchVendorProduct);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Updated successfully'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->vendorProductService->delete($id);
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
