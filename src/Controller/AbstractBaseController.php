<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\Quotation\Service\QuotationService;
use App\Services\User\Domain\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBaseController extends AbstractController
{

    protected const string ACCEPT_COOKIES_SESSION_KEY = 'userHasAcceptedCookies';

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
        $user = $this->getUser();
        if ($user) {
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

        $pageVars['showCookiesMessage'] = $this->shouldShowCookieMessage($request, $user);
        return $pageVars;
    }

    private function shouldShowCookieMessage(Request $request, ?User $user = null): bool {
        if ($this->getUser() && $user instanceof User && $user->hasAcceptedCookies() === true) {
            return false;
        }
        $session = $request->getSession();
        return !$session->get(self::ACCEPT_COOKIES_SESSION_KEY);

    }

}
