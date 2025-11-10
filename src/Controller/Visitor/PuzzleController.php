<?php

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Game\AddPuzzle\ChooseGameDto;
use App\Form\Visitor\Game\AddPuzzle\ChooseGameType;
use App\Services\Puzzle\Service\Interfaces\PuzzleServiceInterface;
use App\Services\Quotation\Service\QuotationService;
use App\Services\User\Domain\User;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PuzzleController extends AbstractBaseController
{

    public function __construct(
        private PuzzleServiceInterface $puzzleService,
        QuotationService $quotationService
    ) {
        parent::__construct($quotationService);
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
        return $this->render('/visitor/puzzles/index.html.twig', $this->populatePageVars($pageVars, $request));
    }



    #[Route('/puzzles/templates', name: 'app.puzzles.template.index')]
    public function templateIndex(Request $request): Response {

        $pageVars = [
            'pageTitle' => 'Puzzle Templates',
            'templates' => $this->puzzleService->getTemplates(),
            'categories' => $this->puzzleService->getAllCategories(),
            'breadcrumbs' => [
                [
                    'route' => 'app.puzzles.template.index',
                    'label' => 'Templates',
                    'active' => true
                ]
            ]
        ];
        return $this->render('/visitor/puzzles/templates/index.html.twig', $this->populatePageVars($pageVars, $request));
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
        return $this->render('/visitor/puzzles/templates/template.html.twig', $this->populatePageVars($pageVars, $request));
    }
    #[Route('/puzzles/templates/{templateSlug}/add-to-game/step-1', name: 'app.puzzles.template.add.step1')]
    public function addToGame(string $templateSlug, Request $request): Response {
        $template = $this->puzzleService->getTemplateBySlug($templateSlug);

        if (!$template) {
            throw $this->createNotFoundException('Template not found');
        }
        $user = $this->getUser();
        if ($user instanceof User) {
            $dto = new ChooseGameDto($template->getSlug());
            $form = $this->createForm(ChooseGameType::class, $dto);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                die('HELLWORLD');
            }
            $pageVars = [
                'pageTitle' => sprintf("Add a %s puzzle to game", $template->getTitle()),
                'form' => $form
            ];

        } else {
            throw new ServerException("User is of incorrect type");
        }

        return $this->render('/visitor/puzzles/templates/addToGame/step1.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/puzzles/categories/{categorySlug}', name: 'app.puzzles.categorySlug.show')]
    public function categoryGet(string $categorySlug, Request $request): Response {
        $category = $this->puzzleService->getCategoryBySlug($categorySlug);

        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }

        $pageVars = [
            'pageTitle' => $category->getLabel(),
            'templates' => $this->puzzleService->getTemplatesByCategory($category),

            'breadcrumbs' => [
                [
                    'route' => 'app.puzzles.template.index',
                    'label' => 'Templates',
                    'active' => false
                ],
                [
                    'label' => $category->getLabel(),
                    'active' => true
                ]
            ]

        ];
        return $this->render('/visitor/puzzles/templates/category.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
