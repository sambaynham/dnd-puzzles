<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;

use App\Form\JoinGameType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractBaseController
{
    #[Route('/account', name: 'app.user.account')]
    public function index(Request $request): Response
    {
        
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        $joinGameForm = $this->createForm(JoinGameType::class);
        $pageVars = [
            'pageTitle' => 'Account',
            'user' => $user,
            'joinGameForm' => $joinGameForm,
        ];
        return $this->render('account/index.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
