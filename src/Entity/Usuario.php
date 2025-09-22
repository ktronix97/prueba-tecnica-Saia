<?php

namespace App\Entity;

use App\Repository\UsuarioRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Enum\Rol;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UsuarioRepository::class)]
class Usuario implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[Groups(['usuario:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['usuario:read'])]
    #[ORM\Column(length: 50)]
    private ?string $primerNombre = null;

    #[Groups(['usuario:read'])]
    #[ORM\Column(length: 50)]
    private ?string $segundoNombre = null;

    #[Groups(['usuario:read'])]
    #[ORM\Column(length: 50)]
    private ?string $primerApellido = null;

    #[Groups(['usuario:read'])]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $segundoApellido = null;

    #[Groups(['usuario:read'])]
    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La contraseña es obligatoria.")]
    #[Assert\Length(min: 8, message: "La contraseña debe tener al menos 8 caracteres.")]
    private ?string $contrasena = null;

    #[Groups(['usuario:read'])]
    #[ORM\Column(type: 'string', enumType: Rol::class)]
    private Rol $rol;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrimerNombre(): ?string
    {
        return $this->primerNombre;
    }

    public function setPrimerNombre(string $primerNombre): static
    {
        $this->primerNombre = $primerNombre;

        return $this;
    }

    public function getSegundoNombre(): ?string
    {
        return $this->segundoNombre;
    }

    public function setSegundoNombre(string $segundoNombre): static
    {
        $this->segundoNombre = $segundoNombre;

        return $this;
    }

    public function getPrimerApellido(): ?string
    {
        return $this->primerApellido;
    }

    public function setPrimerApellido(string $primerApellido): static
    {
        $this->primerApellido = $primerApellido;

        return $this;
    }

    public function getSegundoApellido(): ?string
    {
        return $this->segundoApellido;
    }

    public function setSegundoApellido(?string $segundoApellido): static
    {
        $this->segundoApellido = $segundoApellido;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function setContrasena(string $contrasena): static
    {
        $this->contrasena = $contrasena;

        return $this;
    }

    public function getRol(): Rol
    {
        return $this->rol;
    }

    public function setRol(Rol $rol): static
    {
        $this->rol = $rol;

        return $this;
    }
    
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void 
    {
        // Si se almacenan datos sensibles temporalmente, limpiarlos aquí
        // $this->plainPassword = null;
    }

    public function getPassword(): string
    {
        return $this->contrasena;
    }

    public function getRoles(): array
    {
        return [$this->rol->value];
    }
}
