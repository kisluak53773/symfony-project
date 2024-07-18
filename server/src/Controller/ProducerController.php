<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Producer;

#[Route('/api/producer', name: 'api_producer_')]
class ProducerController extends AbstractController
{
    #[Route(name: 'add', methods: 'post')]
    public function add(ManagerRegistry $registry, Request $request): JsonResponse
    {
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

        $entityManager->persist($producer);
        $entityManager->flush();

        return $this->json(['message' => 'producer created'], 201);
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

        return $this->json(['message' => 'deleted sucseffully'], 204);
    }
}
