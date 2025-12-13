<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\Quotation\Service\QuotationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBaseController extends AbstractController
{
    public function __construct(protected QuotationService $quotationService) {}


    final public function populatePageVars(array $pageVars, Request $request): array {
        $route = $request->get('_route');
        $pageVars['siteName'] = 'The Conundrum Codex';
        $pageVars['nav'] = [
            [
                'route' => 'app.pages.home',
                'label' => 'Home',
                'active' => false
            ],
            [
                'route' => 'app.puzzles.template.index',
                'label' => 'Puzzle Templates',
                'active' => false
            ],
            [
                'route' => 'app.pages.about',
                'label' => 'About',
                'active' => false
            ],

        ];
        if ($this->getUser()) {
            $pageVars['nav'][] = [
                'route' => 'app.games.index',
                'label' => 'My Games',
                'active' => false
            ];
        }
        foreach ($pageVars['nav'] as &$navItem) {
            if ($navItem['route'] === $route) {
                $navItem['active'] = true;
            }
        }
        if (!isset($pageVars['breadcrumbs'])) {
            $pageVars['breadcrumbs'] = [];
        }
        $pageVars['pageClass'] = $pageVars['pageClass'] ?? 'page';
        $pageVars['hideBugReportLink'] = $pageVars['hideBugReportLink'] ?? false;

        $pageVars['quotation'] = $this->quotationService->getRandomQuotation();
        return $pageVars;
    }

}
