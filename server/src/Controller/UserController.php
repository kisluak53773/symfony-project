<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\DTO\User\PatchUserDto;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Contract\Service\UserServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Services\Exception\NotFound\NotFoundException;
use App\Services\Exception\WrongData\WrongDataException;
use App\Services\Exception\Access\AccessForbiddenException;

#[Route('/api/user', name: 'api_user_')]
class UserController extends AbstractController
{
    public function __construct(private UserServiceInterface $userService) {}

    #[Route('/current', name: 'get_current', methods: 'get')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function getCurrentUser(): JsonResponse
    {
        try {
            $user = $this->userService->getCurrentUser();
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(
            data: $user,
            context: [AbstractNormalizer::GROUPS => ['current_user']]
        );
    }

    #[Route(name: 'patch_current_user', methods: 'patch')]
    #[IsGranted(Role::ROLE_USER->value, message: 'You are not allowed to access this route.')]
    public function patchCurrentUser(#[MapRequestPayload] PatchUserDto $patchUserDto): JsonResponse
    {
        try {
            $this->userService->patchCurrentUser($patchUserDto);
        } catch (NotFoundException $e) {
            throw new HttpException(Response::HTTP_NOT_FOUND, $e->getMessage());
        } catch (WrongDataException $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        } catch (AccessForbiddenException $e) {
            throw new HttpException(Response::HTTP_FORBIDDEN, $e->getMessage());
        }

        return $this->json(['message' => 'profile updated'], Response::HTTP_OK);
    }
}
