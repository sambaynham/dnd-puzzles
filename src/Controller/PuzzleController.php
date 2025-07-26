<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PuzzleController extends AbstractBaseController
{
    #[Route('/', name: 'app.puzzles.index')]
    public function index(Request $request): Response
    {
        $pageVars = [
            'pageTitle' => 'Puzzles',
            'puzzles' => [
                [
                    'label' => 'The Hammer of Tharmekhûl',
                    'route' => 'app.puzzles.hammer',
                    'active'=> false
                ]

            ]
        ];
        return $this->render('puzzles/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/puzzles/hammer', name: 'app.puzzles.hammer')]
    public function hammer(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'The Hammer of Tharmekhûl'
        ];
        return $this->render('puzzles/hammer.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
