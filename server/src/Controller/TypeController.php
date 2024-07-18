<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Type;
use App\Services\Uploader\TypesImageUploader;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/api/type', name: 'api_type_')]
class TypeController extends AbstractController
{
    #[Route(name: 'create', methods: 'post')]
    public function add(Request $request, ManagerRegistry $registry, TypesImageUploader $uploader): JsonResponse
    {
        $entityManger = $registry->getManager();

        if (!$request->request->has('title')) {
            return $this->json(['message' => 'insufficient data'], 400);
        }

        $type = new Type();
        $type->setTitle($request->request->get('title'));

        if ($request->files->has('image')) {
            try {
                $imagePath = $uploader->upload($request->files->get('image'));
                $type->setImage($imagePath);
            } catch (FileException $e) {
                return $this->json(['message' => $e->getMessage()], 500);
            }
        }

        $entityManger->persist($type);
        $entityManger->flush();

        return $this->json(['message' => 'type created'], 201);
    }
}
