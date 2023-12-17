<?php

namespace App\Controller;

use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(ParameterBagInterface $parameterBag): Response
    {
        if ($this->getUser())
        {
            return $this->redirectToRoute('app_dashboard');
        }
        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

}
