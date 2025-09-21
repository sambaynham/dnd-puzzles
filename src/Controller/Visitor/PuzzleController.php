<?php

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Services\Puzzle\Service\Interfaces\PuzzleServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PuzzleController extends AbstractBaseController
{

    public function __construct(private PuzzleServiceInterface $puzzleService) {
    }

    #[Route('/puzzles', name: 'app.puzzles.index')]
    public function index(Request $request): Response
    {
        $pageVars = [
            'pageTitle' => 'Puzzles',
            'puzzles' => [
                [
                    'label' => 'The Hammer of Tharmekhûl',
                    'description' => 'Reroute an ancient super-weapon\'s power supply, and smite your enemies!',
                    'route' => 'app.puzzles.hammer',
                    'active'=> false
                ],
                [
                    'label' => 'The Lightforge of Dormuid Fireglad',
                    'description' => 'Some security systems are designed to keep something out. This one keeps something in.',
                    'route' => 'app.puzzles.forge',
                    'active' => true
                ]

            ]
        ];
        return $this->render('puzzles/index.html.twig', $this->populatePageVars($pageVars, $request));
    }



    #[Route('/puzzles/templates', name: 'app.puzzles.template.index')]
    public function templateIndex(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Puzzle Templates',
            'templates' => $this->puzzleService->getTemplates(),
            'breadcrumbs' => [
                [
                    'route' => 'app.puzzles.template.index',
                    'label' => 'Templates',
                    'active' => true
                ]
            ]
        ];
        return $this->render('puzzles/templates/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/puzzles/templates/{templateSlug}', name: 'app.puzzles.template.show')]
    public function templateGet(string $templateSlug, Request $request): Response {
        $template = $this->puzzleService->getTemplateBySlug($templateSlug);

        if (!$template) {
            throw $this->createNotFoundException('Template not found');
        }
        $pageVars = [
            'pageTitle' => $template->getTitle(),
            'template' => $template,

            'breadcrumbs' => [
                [
                    'route' => 'app.puzzles.template.index',
                    'label' => 'Templates',
                    'active' => false
                ],
                [
                    'label' => $template->getTitle(),
                    'active' => true
                ]
            ]

        ];
        return $this->render('puzzles/templates/template.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
