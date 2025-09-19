<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\Puzzle\Service\Interfaces\PuzzleServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractBaseController
{

    public function __construct(private PuzzleServiceInterface $puzzleService) {}

    #[Route('/', name: 'app.pages.home')]
    public function index(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Welcome to the Conundrum Codex!',
            'templates' => $this->puzzleService->getTemplates()
        ];
        return $this->render('pages/index.html.twig', $this->populatePageVars($pageVars, $request));
    }
    #[Route('/contributing', name: 'app.pages.contributing')]
    public function contributing(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Contributing',
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.contributing',
                    'label' => 'Contributing',
                    'active' => true
                ]
            ]
        ];
        return $this->render('pages/contributing.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/about', name: 'app.pages.about')]
    public function about(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'About',
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.about',
                    'label' => 'About',
                    'active' => true
                ]
            ]
        ];
        return $this->render('pages/about.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/coc', name: 'app.pages.coc')]
    public function terms(Request $request): Response {
        $pageVars =[

            'pageTitle' => 'Code of Conduct',
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.coc',
                    'label' => 'Contributing',
                    'active' => true
                ]
            ]
        ];
        return $this->render('pages/terms.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/credits', name: 'app.pages.credits')]
    public function credits(Request $request): Response {
        $pageVars =[
            'pageTitle' => 'Credits',
            'breadcrumbs' => [
                [
                    'route' => 'app.pages.credits',
                    'label' => 'Credits',
                    'active' => true
                ]
            ]
        ];
        return $this->render('pages/credits.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
