<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DateTimeImmutable;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    /**
     * Busca un token no expirado ni revocado cuyo hash coincida.
     */
    public function findValidByRawToken(string $raw): ?RefreshToken
    {
        $all = $this->findBy(['revoked' => false]);
        foreach ($all as $token) {
            if ($token->validateRawToken($raw) && $token->getExpiresAt() > new DateTimeImmutable()) {
                return $token;
            }
        }
        return null;
    }

    /**
     * Elimina tokens expirados y revocados.
     */
    public function purgeExpired(): int
    {
        $qb = $this->createQueryBuilder('t')
            ->delete()
            ->where('t.revoked = true OR t.expiresAt < :now')
            ->setParameter('now', new DateTimeImmutable());

        return $qb->getQuery()->execute();
    }

    public function validate(string $raw): bool
    {
        return password_verify($raw, $this->getTokenHash());
    }

}
