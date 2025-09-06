<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractBaseController extends AbstractController
{
    final public function populatePageVars(array $pageVars, Request $request): array {
        $route = $request->get('_route');

        $pageVars['nav'] = [
            [
                'route' => 'app.puzzles.index',
                'label' => 'Puzzles',
                'active' => false
            ],
            [
                'route' => 'app.pages.contributing',
                'label' => 'Contributing',
                'active' => false
            ],

        ];
        foreach ($pageVars['nav'] as &$navItem) {
            if ($navItem['route'] === $route) {
                $navItem['active'] = true;
            }
        }
        return $pageVars;
    }
}
