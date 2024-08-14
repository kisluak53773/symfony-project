<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\DTO\Vendor\CreateVendorDto;
use App\DTO\Vendor\PatchVendorDto;
use App\Contract\Service\VendorServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

#[Route('/api/vendor', name: 'api_vendor_')]
class VendorController extends AbstractController
{
    public function __construct(private VendorServiceInterface $vendorService) {}

    #[Route(name: 'add', methods: 'post')]
    public function add(#[MapRequestPayload] CreateVendorDto $createVendorDto): JsonResponse
    {
        try {
            $id = $this->vendorService->add($createVendorDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'vendor created', 'id' => $id], Response::HTTP_CREATED);
    }

    #[Route('/current', name: 'get_current_vendor', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getCurrentVendor(): JsonResponse
    {
        try {
            $vendor = $this->vendorService->getCurrentVendor();
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(
            data: $vendor,
            context: [AbstractNormalizer::GROUPS => ['current_vendor']]
        );
    }

    #[Route('/current', name: 'patch_current_vendor', methods: 'patch')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function patchCurrentVendor(#[MapRequestPayload] PatchVendorDto $patchVendorDto): JsonResponse
    {
        try {
            $this->vendorService->patchCurrentVendor($patchVendorDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'vendor updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->vendorService->delete($id);
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
