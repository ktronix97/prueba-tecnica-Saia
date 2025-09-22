<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Enum\Rol;
use App\Entity\Usuario;

#[Route('/api/auth', name: 'api_auth_')]
class AuthController extends AbstractController
{
    public function __construct(
        private AuthService                 $auth,
        private UsuarioRepository           $users,
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface      $em
    ) {}

    #[Route('/registrar', name: 'api_auth_registrar', methods: ['POST'])]
    public function register(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $usuario = new Usuario();
        $usuario->setPrimerNombre($data['primerNombre'] ?? '');
        $usuario->setSegundoNombre($data['segundoNombre'] ?? '');
        $usuario->setPrimerApellido($data['primerApellido'] ?? '');
        $usuario->setSegundoApellido($data['segundoApellido'] ?? null);
        $usuario->setEmail($data['email']);

        // Hashear contraseña
        $hashedPassword = $this->hasher->hashPassword($usuario, $data['password']);
        $usuario->setContrasena($hashedPassword);

        // Rol por defecto
        $usuario->setRol(new Rol($data['rol'] ?? 'ROLE_USER'));

        $this->em->persist($usuario);
        $this->em->flush();

        return $this->json([
            'mensaje' => 'Usuario registrado correctamente',
            'usuario' => [
                'id' => $usuario->getId(),
                'email' => $usuario->getEmail(),
                'rol' => $usuario->getRol(),
            ]
        ], 201);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $req): JsonResponse
    {
        $data = json_decode($req->getContent(), true);
        $user = $this->users->findOneBy(['email' => $data['email']]);

        if (!$user || !$this->hasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['error' => 'Credenciales inválidas'], 401);
        }

        $tokens = $this->auth->login($user);
        return $this->json($tokens);
    }

    #[Route('/refresh-token', methods: ['POST'])]
    public function refresh(Request $req): JsonResponse
    {
        $body    = json_decode($req->getContent(), true);
        $rawRt   = $body['refresh_token'] ?? null;

        if (!$rawRt) {
            return $this->json(['error'=>'Falta refresh_token'], 400);
        }

        try {
            $tokens = $this->auth->refresh($rawRt);
            return $this->json($tokens);
        } catch (\RuntimeException $e) {
            return $this->json(['error'=>$e->getMessage()], 401);
        }
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        // Symfony intercepta y cierra la sesión; aquí no hace falta lógica.
    }
}


