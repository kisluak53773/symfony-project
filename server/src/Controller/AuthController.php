<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\DTO\Auth\RegisterDto;
use App\Contract\Service\AuthServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(private AuthServiceInterface $authService) {}

    #[Route('/register', name: 'register', methods: 'post')]
    public function register(#[MapRequestPayload] RegisterDto $registerDto): JsonResponse
    {
        try {
            $this->authService->register($registerDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'Registered succsessfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'delete', methods: 'delete', requirements: ['id' => '\d+'])]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id): JsonResponse
    {
        try {
            $this->authService->delete($id);
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
