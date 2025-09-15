<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractBaseController
{

    #[Route('/', name: 'app.pages.home')]
    public function index(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Welcome to the Conundrum Codex!'
        ];
        return $this->render('pages/index.html.twig', $this->populatePageVars($pageVars, $request));
    }
    #[Route('/contributing', name: 'app.pages.contributing')]
    public function contributing(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Contributing'
        ];
        return $this->render('pages/contributing.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/about', name: 'app.pages.about')]
    public function about(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'About'
        ];
        return $this->render('pages/about.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/coc', name: 'app.pages.coc')]
    public function terms(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Code of Conduct'
        ];
        return $this->render('pages/terms.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
