<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categorias')]
final class CategoriaController extends AbstractController
{
    #[Route('', name: 'api_categoria_listar', methods: ['GET'])]
    public function listar(CategoriaRepository $categoriaRepository): JsonResponse
    {
        return $this->json($categoriaRepository->findAll(), 200, [], ['groups' => 'categoria:read']);
    }

    #[Route('', name: 'api_categoria_crear', methods: ['POST'])]
    public function crear(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $categoria = new Categoria();
        $categoria->setNombre($data['nombre']);
        $categoria->setDescripcion($data['descripcion'] ?? null);

        $em->persist($categoria);
        $em->flush();

        return $this->json($categoria, 201, [], ['groups' => 'categoria:read']);
    }

    #[Route('/{id}', name: 'api_categoria_visualizar', methods: ['GET'])]
    public function visualizar(Categoria $categoria): JsonResponse
    {
        return $this->json($categoria, 200, [], ['groups' => 'categoria:read']);
    }

    #[Route('/{id}', name: 'api_categoria_editar', methods: ['PUT'])]
    public function editar(Request $request, Categoria $categoria, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $categoria->setNombre($data['nombre'] ?? $categoria->getNombre());
        $categoria->setDescripcion($data['descripcion'] ?? $categoria->getDescripcion());

        $em->flush();

        return $this->json($categoria, 200, [], ['groups' => 'categoria:read']);
    }

    #[Route('/{id}', name: 'api_categoria_eliminar', methods: ['DELETE'])]
    public function eliminar(Categoria $categoria, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($categoria);
        $em->flush();

        return $this->json(['mensaje' => 'Categoría eliminada correctamente'], 200);
    }
}

