<?php
// src/Service/AuthService.php

namespace App\Service;

use App\Entity\Usuario;
use App\Entity\RefreshToken;
use App\Repository\RefreshTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthService
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private RefreshTokenRepository   $rtRepo,
        private EntityManagerInterface   $em
    ) {}

    /**
     * Genera access + refresh, persiste el hash.
     */
    public function login(Usuario $user): array
    {
        $access  = $this->jwtManager->create($user);
        $rawRt   = bin2hex(random_bytes(64));
        $expires = new DateTimeImmutable('+7 days');

        $token   = new RefreshToken($user, $rawRt, $expires);
        $this->em->persist($token);
        $this->em->flush();

        return ['access_token' => $access, 'refresh_token' => $rawRt];
    }

    /**
     * Valida el refresh raw, revoca y devuelve nuevos tokens.
     */
    public function refresh(string $rawRt): array
    {
        $token = $this->rtRepo->findValidByRawToken($rawRt);
        if (!$token) {
            throw new \RuntimeException('Refresh token inválido o expirado');
        }

        $user = $token->getUser();
        $token->revoke();

        $newTokens = $this->login($user);
        $this->em->flush();

        return $newTokens;
    }
}
