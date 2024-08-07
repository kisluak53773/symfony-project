<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Services\Validator\VendorValidator;
use App\Services\Exception\Request\BadRequsetException;
use App\Services\Exception\Request\NotFoundException;
use App\Entity\User;
use App\Entity\Vendor;
use DateTimeImmutable;
use App\Enum\Role;

class VendorService
{
    public function __construct(
        private ManagerRegistry $registry,
        private Request $request,
        private Security $security,
        private VendorValidator $vendorValidator,
        private ValidatorInterface $validator
    ) {
    }

    /**
     * Summary of add
     * @throws \App\Services\Exception\Request\BadRequsetException
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return int
     */
    public function add(): int
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($this->request->getContent());

        $this->vendorValidator->isVendorValid($decoded);

        if (!isset($decoded->userId)) {
            throw new BadRequsetException();
        }

        $userId = $decoded->userId;
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!isset($user)) {
            throw new NotFoundException('Such user does not exist');
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

        $errors = $this->validator->validate($vendor);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($vendor);

        $user->setRoles([Role::ROLE_VENDOR->value]);
        $entityManager->persist($user);

        $entityManager->flush();

        return $vendor->getId();
    }

    /**
     * Summary of getCurrentVendor
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return \App\Entity\Vendor
     */
    public function getCurrentVendor(): Vendor
    {
        $entityManager = $this->registry->getManager();
        $userPhone = $this->security->getUser()->getUserIdentifier();

        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);
        $vendor = $user->getVendor();

        if (!isset($vendor)) {
            throw new NotFoundException();
        }

        return $vendor;
    }

    /**
     * Summary of patchCurrentVendor
     * @throws \App\Services\Exception\Request\BadRequsetException
     * 
     * @return void
     */
    public function patchCurrentVendor(): void
    {
        $entityManager = $this->registry->getManager();
        $decoded = json_decode($this->request->getContent());

        $this->vendorValidator->isVendorValidForPatch($decoded);

        $userPhone = $this->security->getUser()->getUserIdentifier();
        $user = $entityManager->getRepository(User::class)->findOneBy(['phone' => $userPhone]);

        $vendor = $user->getVendor();
        $vendor->setTitle($decoded->title);
        $vendor->setAddress($decoded->address);
        $vendor->setInn($decoded->inn);
        $vendor->setRegistrationAuthority($decoded->registrationAuthority);

        $errors = $this->validator->validate($vendor);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new BadRequsetException($errorsString);
        }

        $entityManager->persist($vendor);
        $entityManager->flush();
    }

    /**
     * Summary of delete
     * @param int $id
     * 
     * @throws \App\Services\Exception\Request\NotFoundException
     * 
     * @return void
     */
    public function delete(int $id): void
    {
        $entityManager = $this->registry->getManager();

        $vendor = $entityManager->getRepository(Vendor::class)->find($id);

        if (!isset($vendor)) {
            throw new NotFoundException('No such vendor exist');
        }

        $entityManager->remove($vendor);
        $entityManager->flush();
    }
}
