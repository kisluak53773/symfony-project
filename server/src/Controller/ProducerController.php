<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Producer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/producer', name: 'api_producer_')]
class ProducerController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    public function add(
        ManagerRegistry $registry,
        Request $request,
        ValidatorInterface $validator
    ): JsonResponse {
        $entityManager = $registry->getManager();
        $decoded = json_decode($request->getContent());

        if (!isset($decoded->title) || !isset($decoded->country) || !isset($decoded->address)) {
            return $this->json(['message' => 'insufficient data provided'], 400);
        }

        $title = $decoded->title;
        $country = $decoded->country;
        $address = $decoded->address;

        $producer = new Producer();
        $producer->setTitle($title);
        $producer->setCountry($country);
        $producer->setAddress($address);

        $errors = $validator->validate($producer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return $this->json(['message' => $errorsString], 400);
        }

        $entityManager->persist($producer);
        $entityManager->flush();

        return $this->json(['message' => 'producer created'], 201);
    }

    #[Route('/vendor', name: 'get_vendor', methods: 'get')]
    public function getForVendor(ManagerRegistry $registry): JsonResponse
    {
        $entityManager = $registry->getManager();
        $producers = $entityManager->getRepository(Producer::class)->findAll();

        return $this->json(
            data: $producers,
            context: [AbstractNormalizer::GROUPS => ['vendor_producer']]
        );
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: 'delete')]
    public function delete(int $id, ManagerRegistry $managerRegistry): JsonResponse
    {
        $entityManager = $managerRegistry->getManager();

        $producer = $entityManager->getRepository(Producer::class)->find($id);

        if (!isset($producer)) {
            return $this->json(['message' => 'no such producer exist'], 404);
        }

        $entityManager->remove($producer);
        $entityManager->flush();

        return $this->json(['message' => 'deleted sucseffully', 'id' => $producer->getId()], 204);
    }
}
