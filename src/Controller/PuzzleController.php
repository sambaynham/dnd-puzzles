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
                ],
                [
                    'label' => 'The Lightforge of Dormuid Fireglad',
                    'route' => 'app.puzzles.forge',
                    'active' => false
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


    #[Route('/puzzles/forge', name: 'app.puzzles.forge')]
    public function forge(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'The Lightforge'
        ];
        return $this->render('puzzles/lightforge.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
