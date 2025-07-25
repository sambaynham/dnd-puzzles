<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PuzzleController extends AbstractController
{
    #[Route('/', name: 'app.puzzles.index')]
    public function index(): Response
    {
        $pageVars = [
            'pageTitle' => 'Puzzles',
            'puzzles' => [
                [
                    'label' => 'The Hammer of Tharmekhûl',
                    'route' => 'app.puzzles.hammer'
                ]

            ]
        ];
        return $this->render('puzzles/index.html.twig', $pageVars);
    }

    #[Route('/puzzles/hammer', name: 'app.puzzles.hammer')]
    public function hammer(): Response {
        $pageVars =[
            'pageTitle' => 'The Hammer of Tharmekhûl'
        ];
        return $this->render('puzzles/hammer.html.twig', $pageVars);
    }
}
