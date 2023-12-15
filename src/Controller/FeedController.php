<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    #[Route('/dashboard/feed', name: 'app_feed')]
    public function index(): Response
    {
        return $this->render('dashboard/feed.html.twig', [
            'controller_name' => 'FeedController',
        ]);
    }
}
