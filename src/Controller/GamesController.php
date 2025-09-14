<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GamesController extends AbstractBaseController
{
    #[Route('/games', name: 'app.games.index')]
    public function index(Request $request): Response
    {
        $pageVars = [
            'pageTitle' => 'My Games',
        ];
        return $this->render('games/index.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
