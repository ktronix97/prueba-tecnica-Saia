<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RefreshTokenController extends AbstractController
{
    #[Route('/refresh/token', name: 'app_refresh_token')]
    public function index(): Response
    {
        return $this->render('refresh_token/index.html.twig', [
            'controller_name' => 'RefreshTokenController',
        ]);
    }
}
