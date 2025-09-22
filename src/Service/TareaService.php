<?php

namespace App\Service;

use App\Entity\Tarea;
use App\Repository\TareaRepository;

class TareaService
{
    public function __construct(
        private readonly TareaRepository $tareaRepository
    ) {}

    /**
     * Lista tareas según filtros dinámicos.
     *
     * @param array{
     *   estado?: string,
     *   prioridad?: string,
     *   usuario?: int,
     *   fecha_inicio?: string,
     *   fecha_fin?: string,
     *   ordenar_por?: string,
     *   orden?: string
     * } $filtros
     */
    public function listar(array $filtros): array
    {
        $tareas = $this->tareaRepository->findByFilters($filtros);
        return array_map(fn(Tarea $t) => [
            'id'              => $t->getId(),
            'titulo'          => $t->getTitulo(),
            'estado'          => $t->getEstado()->value,
            'prioridad'       => $t->getPrioridad()->value,
            'fechaVencimiento'=> $t->getFechaVencimiento()?->format('Y-m-d'),
            'asignadoA'       => $t->getAsignadoA()?->getId(),
            'categorias'      => $t->getCategorias()->map(fn($c)=>$c->getId())->toArray(),
        ], $tareas);
    }
}