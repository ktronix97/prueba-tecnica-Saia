<?php

namespace App\Repository;

use App\Entity\Tarea;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<Tarea>
 */
class TareaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $reg)
    {
        parent::__construct($reg, Tarea::class);
    }

    /**
     * Encuentra tareas por filtros opcionales:
     * estado, prioridad, usuario, rango de fechas.
     */
    public function findByFilters(array $filtros): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.categorias', 'c')
            ->addSelect('c');

        if (!empty($filtros['estado'])) {
            $qb->andWhere('t.estado = :estado')
            ->setParameter('estado', $filtros['estado']);
        }

        if (!empty($filtros['prioridad'])) {
            $qb->andWhere('t.prioridad = :prioridad')
            ->setParameter('prioridad', $filtros['prioridad']);
        }

        if (!empty($filtros['usuario'])) {
            $qb->andWhere('t.asignadoA = :usr')
            ->setParameter('usr', $filtros['usuario']);
        }
        // Filtrado por rango de fechas
        if (! empty($filtros['fecha_inicio']) && ! empty($filtros['fecha_fin'])) {
            $inicio = (new DateTimeImmutable($filtros['fecha_inicio']))
            ->setTime(0, 0, 0);
            $fin = DateTimeImmutable::createFromFormat('Y-m-d', $filtros['fecha_fin'])
            ->setTime(23, 59, 59);
            $qb->andWhere('t.fechaCreacion BETWEEN :inicio AND :fin')
            ->setParameter('inicio', $inicio)
            ->setParameter('fin',    $fin);
        }

        // Ordenamiento dinámico
        if (!empty($filtros['ordenar_por'])) {
            $qb->orderBy('t.' . $filtros['ordenar_por'], $filtros['orden'] ?? 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Retorna un conteo rápido de tareas por estado (para EXPLAIN).
     */
    public function countByEstado(): array
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t.estado, COUNT(t.id) as total')
            ->groupBy('t.estado');

        return $qb->getQuery()->getArrayResult();
    }
}
