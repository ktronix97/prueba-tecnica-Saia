<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Enum\Rol;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/api/usuarios')]
final class UsuarioController extends AbstractController
{
    #[Route('', name: 'api_usuario_listar', methods: ['GET'])]
    public function listar(UsuarioRepository $usuarioRepository): JsonResponse
    {
        return $this->json($usuarioRepository->findAll(), 200, [], ['groups' => 'usuario:read']);
    }

    #[Route('', name: 'api_usuario_crear', methods: ['POST'])]
    public function crear(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $usuario = new Usuario();
        $usuario->setPrimerNombre($data['primerNombre']);
        $usuario->setSegundoNombre($data['segundoNombre'] ?? '');
        $usuario->setPrimerApellido($data['primerApellido']);
        $usuario->setSegundoApellido($data['segundoApellido'] ?? null);
        $usuario->setEmail($data['email']);

        // Hashear contraseña
        $hashedPassword = $passwordHasher->hashPassword($usuario, $data['contrasena']);
        $usuario->setContrasena($hashedPassword);

        // Rol por defecto
        $usuario->setRol(new Rol($data['roles'] ?? ['ROLE_USER']));

        $em->persist($usuario);
        $em->flush();

        return $this->json(['mensaje' => 'Usuario creado correctamente'], 201);
    }

    #[Route('/{id}', name: 'api_usuario_visualizar', methods: ['GET'])]
    public function visualizar(Usuario $usuario): JsonResponse
    {
        return $this->json($usuario, 200, [], ['groups' => 'usuario:read']);
    }

    #[Route('/{id}', name: 'api_usuario_editar', methods: ['PUT'])]
    public function editar(
        Request $request,
        Usuario $usuario,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $usuario->setPrimerNombre($data['primerNombre'] ?? $usuario->getPrimerNombre());
        $usuario->setSegundoNombre($data['segundoNombre'] ?? $usuario->getSegundoNombre());
        $usuario->setPrimerApellido($data['primerApellido'] ?? $usuario->getPrimerApellido());
        $usuario->setSegundoApellido($data['segundoApellido'] ?? $usuario->getSegundoApellido());
        $usuario->setEmail($data['email'] ?? $usuario->getEmail());

        if (!empty($data['contrasena'])) {
            $hashedPassword = $passwordHasher->hashPassword($usuario, $data['contrasena']);
            $usuario->setContrasena($hashedPassword);
        }

        if (!empty($data['rol'])) {
            $usuario->setRol(new Rol($data['rol']));
        }

        $em->flush();

        return $this->json(['mensaje' => 'Usuario actualizado correctamente']);
    }

    #[Route('/{id}', name: 'api_usuario_eliminar', methods: ['DELETE'])]
    public function eliminar(Usuario $usuario, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($usuario);
        $em->flush();

        return $this->json(['mensaje' => 'Usuario eliminado correctamente']);
    }
}

