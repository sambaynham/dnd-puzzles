<?php

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HammerController extends AbstractBaseController
{
    #[Route('/puzzles/hammer', name: 'app.puzzles.hammer')]
    public function hammer(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'The Hammer of Tharmekhûl'
        ];
        return $this->render('/visitor/puzzles/hammer.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
