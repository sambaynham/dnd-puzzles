<?php

declare(strict_types=1);

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Form\Game\JoinGameType;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccountController extends AbstractBaseController
{
    public function __construct(private GameInvitationRepository $gameInvitationRepository) {

    }
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
            'invitations' => $this->gameInvitationRepository->getOutstandingInvitationsForUser($user)
        ];
        return $this->render('/visitor/account/index.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
