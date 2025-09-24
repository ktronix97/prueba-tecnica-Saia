<?php


namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Usuario;
use DateTimeInterface;
use DateTime;

/**
 * Cada token de refresco se guarda con hash, fecha de creación,
 * fecha de expiración y un flag de revocado.
 */
#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\Table(name: 'refresh_tokens')]
class RefreshToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'refreshTokens')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Usuario $user;

    #[ORM\Column(type: 'string', length: 255)]
    private string $tokenHash;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $expiresAt;

    #[ORM\Column(type: 'boolean')]
    private bool $revoked = false;

    public function __construct(Usuario $user, string $rawToken, DateTimeInterface $expiresAt)
    {
        $this->user       = $user;
        $this->tokenHash  = password_hash($rawToken, PASSWORD_DEFAULT);
        $this->createdAt  = new DateTime();
        $this->expiresAt  = $expiresAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): Usuario
    {
        return $this->user;
    }

    public function getTokenHash(): string
    {
        return $this->tokenHash;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): self
    {
        $this->revoked = true;
        return $this;
    }

    /**
     * Verifica el token raw contra el hash guardado
     */
    public function validateRawToken(string $rawToken): bool
    {
        return password_verify($rawToken, $this->tokenHash);
    }
}
