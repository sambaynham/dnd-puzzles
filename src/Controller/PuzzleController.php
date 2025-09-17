<?php

namespace App\Controller;

use App\Services\Puzzle\Service\Interfaces\PuzzleServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    #[Route('/puzzles/categories/{categorySlug}', name: 'app.puzzles.category.show')]
    public function category(string $categorySlug, Request $request): Response {
        $category = $this->puzzleService->getCategoryBySlug($categorySlug);
        if (!$category) {
            throw $this->createNotFoundException('Category not found');
        }
        $pageVars = [
            'pageTitle' => sprintf('%s Puzzles', $category->getLabel(),),
            'category' => $category,
        ];
        return $this->render('puzzles/categories/category.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
