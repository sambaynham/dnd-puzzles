<?php

namespace App\Controller\Visitor;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/puzzles/arcane')]
class ArcaneController extends AbstractController
{
    #[Route('/switches', name: 'arcane_switches')]
    public function switches(): Response
    {
        return $this->render('visitor/puzzles/arcane/switches.html.twig');
    }

    #[Route('/potions', name: 'arcane_potions')]
    public function potions(): Response
    {
        return $this->render('visitor/puzzles/arcane/potions.html.twig');
    }

    #[Route('/rings', name: 'arcane_rings')]
    public function rings(): Response
    {
        return $this->render('visitor/puzzles/arcane/rings.html.twig');
    }
}
