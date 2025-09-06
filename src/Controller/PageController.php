<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractBaseController
{
    #[Route('/contributing', name: 'app.pages.contributing')]
    public function contributing(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Contributing'
        ];
        return $this->render('pages/contributing.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
