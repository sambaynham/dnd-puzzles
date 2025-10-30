<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractBaseController
{
    #[Route('admin/dashboard', name: 'admin.dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Dashboard',
        ];
        return $this->render('admin/dashboard.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
