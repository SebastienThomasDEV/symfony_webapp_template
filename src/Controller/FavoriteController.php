<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavoriteController extends AbstractController
{
    #[Route('/dashboard/favorites', name: 'app_favorites')]
    public function index(): Response
    {
        return $this->render('dashboard/favorites.html.twig', [
            'controller_name' => 'FavoriteController',
        ]);
    }
}
