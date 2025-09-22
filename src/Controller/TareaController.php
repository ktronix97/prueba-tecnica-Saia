<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Tarea;
use App\Repository\TareaRepository;
use App\Enum\Estado;
use App\Enum\Prioridad;
use App\Entity\Categoria;
use App\Entity\Usuario;

#[Route('/api/tareas')]
final class TareaController extends AbstractController
{
    #[Route('', name: 'api_tarea_visualizar', methods: ['GET'])]
    public function visualizar(TareaRepository $tareaRepository): JsonResponse
    {
        return $this->json($tareaRepository->findAll(), Response::HTTP_OK, [], ['groups' => 'tarea:read']);
    }

    #[Route('', name: 'api_tarea_crear', methods: ['POST'])]
    public function crear(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $tarea = new Tarea();
        $tarea->setTitulo($data['titulo']);
        $tarea->setDescripcion($data['descripcion'] ?? null);
        $tarea->setEstado(Estado::from($data['estado']));
        $tarea->setPrioridad(Prioridad::from($data['prioridad']));
        $tarea->setFechaVencimiento(isset($data['fechaVencimiento']) ? new \DateTime($data['fechaVencimiento']) : null);

        // Relación con Usuario
        $usuario = $em->getReference(Usuario::class, $data['asignadoA']);
        $tarea->setAsignadoA($usuario);

        // Relación con Categorías
        if (!empty($data['categorias'])) {
            foreach ($data['categorias'] as $categoriaId) {
                $categoria = $em->getReference(Categoria::class, $categoriaId);
                $tarea->addCategoria($categoria);
            }
        }

        $em->persist($tarea);
        $em->flush();

        return $this->json($tarea, Response::HTTP_CREATED, [], ['groups' => 'tarea:read']);
    }

    #[Route('/{id}', name: 'api_tarea_editar', methods: ['PUT'])]
    public function editar(Request $request, Tarea $tarea, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $tarea->setTitulo($data['titulo'] ?? $tarea->getTitulo());
        $tarea->setDescripcion($data['descripcion'] ?? $tarea->getDescripcion());
        $tarea->setEstado(isset($data['estado']) ? Estado::from($data['estado']) : $tarea->getEstado());
        $tarea->setPrioridad(isset($data['prioridad']) ? Prioridad::from($data['prioridad']) : $tarea->getPrioridad());
        $tarea->setFechaVencimiento(isset($data['fechaVencimiento']) ? new \DateTime($data['fechaVencimiento']) : $tarea->getFechaVencimiento());

        if (isset($data['asignadoA'])) {
            $usuario = $em->getReference(Usuario::class, $data['asignadoA']);
            $tarea->setAsignadoA($usuario);
        }

        if (isset($data['categorias'])) {
            $tarea->getCategorias()->clear();
            foreach ($data['categorias'] as $categoriaId) {
                $categoria = $em->getReference(Categoria::class, $categoriaId);
                $tarea->addCategoria($categoria);
            }
        }

        $em->flush();

        return $this->json($tarea, Response::HTTP_OK, [], ['groups' => 'tarea:read']);
    }

    #[Route('/{id}', name: 'api_tarea_eliminar', methods: ['DELETE'])]
    public function eliminar(Tarea $tarea, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($tarea);
        $em->flush();

        return $this->json(['mensaje' => 'Tarea eliminada'], Response::HTTP_OK);
    }
}

