<?php

namespace App\Controller\Visitor;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Controller\AbstractBaseController;
use App\Dto\Game\CreateGameDto;
use App\Entity\Game;
use App\Entity\User;
use App\Form\Game\CreateGameType;
use App\Form\Game\DeleteGameType;
use App\Security\GameManagerVoter;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class GamesController extends AbstractBaseController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly GameInvitationRepository $gameInvitationRepository
    ) {

    }

    #[IsGranted('ROLE_USER')]
    #[Route('/games', name: 'app.games.index')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('login');
        }
        $pageVars = [
            'pageTitle' => 'My Games',
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => true
                ]
            ],
            'gamesMastered' => $user->getGamesMastered(),
            'gamesMember' => $user->getGames()
        ];
        return $this->render('/visitor/games/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/games/create', name: 'app.games.create')]
    public function create(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to create a game.');
        }
        $dto = new CreateGameDto();
        $form = $this->createForm(CreateGameType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $game = new Game(
                name: $dto->name,
                slug: $dto->slug,
                description: $dto->description,
                gamesMaster: $user,
            );
            $success = true;
            try {
                $this->validator->validate($game);
            } catch (ValidationException $e) {
                $violations = $e->getConstraintViolationList();
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
                $success = false;
            }

            if ($success) {
                $this->entityManager->persist($game);
                $this->entityManager->flush();
                $this->addFlash('success', 'Game created successfully.');
                return $this->redirectToRoute('app.games.manage', ['slug' => $game->getSlug()]);
            }

        }
        $pageVars = [
            'pageTitle' => 'Create a Game',
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => 'Create a game',
                    'active' => true
                ]
            ],
            'form' => $form
        ];
        return $this->render('/visitor/games/create.html.twig', $this->populatePageVars($pageVars, $request));
    }


    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{slug}/manage', name: 'app.games.manage')]
    public function manage(
        Game $game,
        Request $request
    ) {
        $title = sprintf('Manage %s', $game->getName());
        $invitations = $this->gameInvitationRepository->getOutstandingInvitationsForGame($game);
        $pageVars = [
            'pageTitle' => $title,
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => $title,
                    'active' => true
                ],
            ],
            'game' => $game,
            'invitations' => $invitations
        ];
        return $this->render('games/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }


    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{slug}/delete', name: 'app.games.delete')]
    public function delete(
        Game $game,
        Request $request
    ) {
        $title = sprintf('Really delete %s?', $game->getName());
        $form = $this->createForm(DeleteGameType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($game);
            $this->entityManager->flush();
            $this->addFlash('success', sprintf("Game %s delete successfully", $game->getName()));
            return $this->redirectToRoute('app.games.index');
        }
        $pageVars = [
            'pageTitle' => $title,
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => $title,
                    'active' => true
                ],
            ],
            'form' => $form
        ];
        return $this->render('games/delete.html.twig', $this->populatePageVars($pageVars, $request));
    }

}
