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
use App\Constants\RoleConstants;

#[Route('/api/vendor', name: 'api_vendor_')]
class VendorController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    public function add(
        Request $request,
        ManagerRegistry $managerRegistry,
        VendorValidator $validator
    ): JsonResponse {
        $entityManager = $managerRegistry->getManager();
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->userId) || !$validator->isVendorValid($decoded)) {
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
        $entityManager->persist($vendor);

        $user->setRoles([RoleConstants::ROLE_VENDOR]);
        $entityManager->persist($user);

        $entityManager->flush();

        return $this->json(['message' => 'vendor created'], 201);
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
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
