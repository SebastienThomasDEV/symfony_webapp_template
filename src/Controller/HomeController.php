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
        $parser = new Parser();
        $pdf = $parser->parseFile($parameterBag->get('kernel.project_dir') . '/public/test.pdf');
        $forms = [];
        foreach ($pdf->getObjects() as $obj) {
            if (is_a($obj, 'Smalot\PdfParser\XObject\Form') && $obj->getText() !== ' ' && trim($obj->getText()) !== '4') {
//                $forms["{$obj->getBBox()->xMin}x{$obj->getBBox()->yMin}"] = $obj->getText();
//                preg_replace('/\t+/', '', trim($obj->getText()));
                $forms[] = trim($obj->getText());
            }
        }
        dd($forms);
        return $this->render('test.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

}
