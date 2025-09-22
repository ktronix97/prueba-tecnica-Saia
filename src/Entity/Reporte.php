<?php

namespace App\Entity;

use App\Repository\ReporteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Tarea;

#[ORM\Entity(repositoryClass: ReporteRepository::class)]
class Reporte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "El contenido es obligatorio.")]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenido = null;

    #[ORM\Column]
    private ?\DateTime $fecha = null;
    
    // Relación con la tarea reportada
    #[ORM\ManyToOne(targetEntity: Tarea::class, inversedBy: 'reportes')]
    #[ORM\JoinColumn(nullable: false)]
    private Tarea $tarea;

    public function __construct()
    {
    $this->fecha = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenido(): ?string
    {
        return $this->contenido;
    }

    public function setContenido(string $contenido): static
    {
        $this->contenido = $contenido;

        return $this;
    }

    public function getFecha(): ?\DateTime
    {
        return $this->fecha;
    }

    public function setFecha(\DateTime $fecha): static
    {
        $this->fecha = $fecha;
        return $this;
    }

    public function getTarea(): ?Tarea
    {
        return $this->tarea;
    }

    public function setTarea(?Tarea $tarea): static
    {
        $this->tarea = $tarea;
        return $this;
    }

}
