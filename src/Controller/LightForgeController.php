<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LightForgeController extends AbstractBaseController
{
    #[Route('/puzzles/lightforge', name: 'app.puzzles.forge')]
    public function hammer(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'The Lightforge of Dormuid Fireglad'
        ];
        return $this->render('puzzles/lightforge.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
