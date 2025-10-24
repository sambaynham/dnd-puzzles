<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBaseController extends AbstractController
{
    private const array QUOTATIONS = [
        [
            'text' => 'Fight fire with Fireball',
            'citation' => 'Evocation Magic, a Primer'
        ],
        [
            'text' => 'I\'d quite like to rage now, if that\'s alright with you.',
            'citation' => 'Bloodfang the unfailingly polite'
        ],
        [
            'text' => 'Hot mindflayers in your area are waiting to eat you!',
            'citation' => 'Jourval the Obvious'
        ],
        [
            'text' => 'We\'re three miles underground, Ugthruk. It was not \'just the wind\'. Check again.',
            'citation' => 'Dregthorth Rogues-bane'
        ],
        [
            'text' => 'The more vowels you have in your name, the more Elvish you are. Random punctuation never hurt either.',
            'citation' => 'Anduri\'eauől the Unpronounceable'
        ]
    ];

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
        $pageVars['heroImagePath'] = $this->getRandomHeroImagePath();
        $pageVars['hideBugReportLink'] = $pageVars['hideBugReportLink'] ?? false;

        $quotation = self::QUOTATIONS[array_rand(self::QUOTATIONS)];

        $pageVars['quotation'] = $quotation['text'];
        $pageVars['citation'] = $quotation['citation'];
        return $pageVars;
    }

    private function getRandomHeroImagePath(): string {
        $paths = [
            '/dist/images/hero3.webp',
            '/dist/images/hero2.webp',
        ];
        return $paths[array_rand($paths, 1)];
    }
}
