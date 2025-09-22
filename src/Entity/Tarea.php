<?php

namespace App\Entity;

use App\Repository\TareaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use App\Entity\Usuario;
use App\Entity\Categoria;
use App\Entity\Reporte;
use App\Enum\Estado;
use App\Enum\Prioridad;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TareaRepository::class)]
class Tarea
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "El título es obligatorio.")]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descripcion = null;

    #[Groups(['tarea:read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    #[ORM\Column]
    private ?\DateTime $fechaCreacion = null;

    #[Groups(['tarea:read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    #[ORM\Column]
    private ?\DateTime $ultimaModificacion = null;

    #[ORM\Column(length: 20)]
    private ?string $estado = null;

    #[ORM\Column(length: 10)]
    private ?string $prioridad = null;

    #[Groups(['tarea:read'])]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d H:i:s'])]
    #[ORM\Column(nullable: true)]
    private ?\DateTime $fechaVencimiento = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Usuario $asignadoA;

    #[ORM\ManyToMany(targetEntity: Categoria::class)]
    private Collection $categorias;

    #[ORM\OneToMany(mappedBy: 'tarea', targetEntity: Reporte::class, cascade: ['persist', 'remove'])]
    private Collection $reportes;

    public function __construct()
    {
        $this->reportes = new ArrayCollection();
        $this->categorias = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function establecerFechaCreacion(): void
    {
        $this->fechaCreacion = new \DateTime();
        $this->ultimaModificacion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): static
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getFechaCreacion(): ?\DateTime
    {
        return $this->fechaCreacion;
    }

    public function getUltimaModificacion(): ?\DateTime
    {
        return $this->ultimaModificacion;
    }

    #[ORM\PreUpdate]
    public function actualizarUltimaModificacion(): void
    {
        $this->ultimaModificacion = new \DateTime();
    }

    public function getEstado(): ?Estado
    {
        return $this->estado ? Estado::from($this->estado) : null;
    }

    public function setEstado(Estado $estado): static
    {
        $this->estado = $estado->value;

        return $this;
    }

    public function getPrioridad(): ?Prioridad
    {
        return $this->prioridad ? Prioridad::from($this->prioridad) : null;
    }

    public function setPrioridad(Prioridad $prioridad): static
    {
        $this->prioridad = $prioridad->value;

        return $this;
    }

    public function getFechaVencimiento(): ?\DateTime
    {
        return $this->fechaVencimiento;
    }

    public function setFechaVencimiento(?\DateTime $fechaVencimiento): static
    {
        $this->fechaVencimiento = $fechaVencimiento;

        return $this;
    }

    /**
     * @return Collection<int, Categoria>
     */
    public function getCategorias(): Collection
    {
        return $this->categorias;
    }

    public function addCategoria(Categoria $categoria): static
    {
        if (!$this->categorias->contains($categoria)) {
            $this->categorias->add($categoria);
            $categoria->addTarea($this); 
    }

        return $this;    
    }

    public function removeCategoria(Categoria $categoria): static
    {
        if ($this->categorias->removeElement($categoria)) {
            $categoria->removeTarea($this); // sincroniza el lado inverso
        }

        return $this;
    }

    public function getAsignadoA(): ?Usuario
    {
        return $this->asignadoA;
    }

    public function setAsignadoA(Usuario $asignadoA): static
    {
        $this->asignadoA = $asignadoA;

        return $this;
    }

    public function addReporte(Reporte $reporte): static
    {
        if (!$this->reportes->contains($reporte)) {
            $this->reportes->add($reporte);
            $reporte->setTarea($this);
        }

        return $this;
    }

    public function removeReporte(Reporte $reporte): static
    {
        if ($this->reportes->removeElement($reporte)) {
            if ($reporte->getTarea() === $this) {
                $reporte->setTarea(null);
            }
        }

        return $this;
    }
}