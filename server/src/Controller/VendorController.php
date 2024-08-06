<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;
use App\Entity\Vendor;
use App\Services\Validator\VendorValidator;
use DateTimeImmutable;
use App\Enum\Role;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/vendor', name: 'api_vendor_')]
class VendorController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    public function add(
        Request $request,
        ManagerRegistry $managerRegistry,
        VendorValidator $vendorValidator,
        ValidatorInterface $validator

    ): JsonResponse {
        $entityManager = $managerRegistry->getManager();
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->userId) || !$vendorValidator->isVendorValid($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $userId = $decoded->userId;
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!isset($user)) {
            return $this->json(['message' => 'such user does not exist'], 400);
        }

        $title = $decoded->title;
        $vendorAddress = $decoded->vendorAddress;
        $inn = $decoded->inn;
        $registrationAuthority = $decoded->registrationAuthority;
        $registraionDate = DateTimeImmutable::createFromFormat('Y-m-d', $decoded->registrationDate);
        $registrationCertificateDate = DateTimeImmutable::createFromFormat('Y-m-d', $decoded->registrationCertificateDate);

        $vendor = new Vendor();
        $vendor->setTitle($title);
        $vendor->setAddress($vendorAddress);
        $vendor->setInn($inn);
        $vendor->setRegistrationAuthority($registrationAuthority);
        $vendor->setRegistrationDate($registraionDate);
        $vendor->setRegistrationCertificateDate($registrationCertificateDate);
        $vendor->setUser($user);

        $errors = $validator->validate($vendor);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($vendor);

        $user->setRoles([Role::ROLE_VENDOR->value]);
        $entityManager->persist($user);

        $entityManager->flush();

        return $this->json(['message' => 'vendor created', 'id' => $vendor->getId()], 201);
    }

    #[Route('/current', name: 'get_current_vendor', methods: 'get')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function getCurrentVendor(
        ManagerRegistry $doctrine,
        Security $security
    ): JsonResponse {
        $entityManager = $doctrine->getManager();
        $userPhone = $security->getUser()->getUserIdentifier();

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $vendor = $user->getVendor();

        return $this->json(
            data: $vendor,
            context: [AbstractNormalizer::GROUPS => ['current_vendor']]
        );
    }

    #[Route('/current', name: 'patch_current_vendor', methods: 'patch')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function patchCurrentVendor(
        ManagerRegistry $doctrine,
        Request $request,
        Security $security,
        VendorValidator $vendorValidator,
        ValidatorInterface $validator
    ): JsonResponse {
        $entityManager = $doctrine->getManager();
        $decoded = json_decode($request->getContent());

        if (!$vendorValidator->isVendorValidForPatch($decoded)) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $userPhone = $security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $vendor = $user->getVendor();
        $vendor->setTitle($decoded->title);
        $vendor->setAddress($decoded->address);
        $vendor->setInn($decoded->inn);
        $vendor->setRegistrationAuthority($decoded->registrationAuthority);

        $errors = $validator->validate($vendor);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($vendor);
        $entityManager->flush();

        return $this->json(['message' => 'vendor updated'], 200);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    #[IsGranted(Role::ROLE_VENDOR->value, message: 'You are not allowed to access this route.')]
    public function delete(int $id, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $vendor = $entityManager->getRepository(Vendor::class)->find($id);

        if (!isset($vendor)) {
            return $this->json(['message' => 'no such vendor exist'], 404);
        }

        $entityManager->remove($vendor);
        $entityManager->flush();

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
