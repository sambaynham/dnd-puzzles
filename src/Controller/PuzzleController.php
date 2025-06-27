<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PuzzleController extends AbstractController
{
    #[Route('/', name: 'app.index')]
    public function index(): Response
    {
        $pageVars =[];
        return $this->render('index.html.twig', $pageVars);
    }
}
