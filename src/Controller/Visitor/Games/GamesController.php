<?php

namespace App\Controller\Visitor\Games;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Controller\AbstractBaseController;
use App\Controller\Traits\HandlesImageUploadsTrait;
use App\Dto\Visitor\Game\GameDto;
use App\Form\Visitor\Game\GameType;
use App\Form\Visitor\Game\DeleteGameType;
use App\Security\GameManagerVoter;
use App\Security\GamePlayerVoter;
use App\Services\Game\Domain\Game;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class GamesController extends AbstractBaseController
{
    use HandlesImageUploadsTrait;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly GameServiceInterface $gameService,
        protected SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/images/games')] private string $publicImagesDirectory,

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
        $dto = new GameDto();
        $form = $this->createForm(GameType::class, $dto);
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
                return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
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
    #[Route('games/{gameSlug}/manage', name: 'app.games.manage')]
    public function manage(
        Game $game,
        Request $request
    ): Response
    {
        $title = sprintf('Manage %s', $game->getName());
        $invitations = $this->gameService->getOutstandingInvitationsForGame($game);
        $pageVars = [
            'pageTitle' => $title,
            'heroImageUrl' => $game->getHeroImageUrl(),
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
        return $this->render('/visitor/games/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/edit-info', name: 'app.games.edit')]
    public function editInfo(Game $game, Request $request): Response {
        $dto = GameDto::makeFromGame($game);
        $form = $this->createForm(GameType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('heroImageUrl')->getData();
            if ($imageFile) {
                $heroImageUrl = $this->handleImageUpload($imageFile, $this->publicImagesDirectory);
                $game->setHeroImageUrl($heroImageUrl);
            }
            $game->setName($dto->name);
            $game->setDescription($dto->description);
            $this->entityManager->persist($game);
            $this->entityManager->flush();
            $this->addFlash('success', 'Game updated successfully.');
            return $this->redirectToRoute('app.games.manage', ['gameSlug' => $game->getSlug()]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Edit %s's basic information", $game->getName()),
            'form' => $form
        ];
        return $this->render('/visitor/games/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }



    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/delete', name: 'app.games.delete')]
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
        return $this->render('/visitor/games/delete.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GamePlayerVoter::PLAY_GAME, 'game')]
    #[Route('games/{gameSlug}/play', name: 'app.games.play')]
    public function play(
        Game $game,
        Request $request
    ) {
        $pageVars = [
            'pageTitle' => sprintf("Play %s", $game->getName()),
            'heroImageUrl' => $game->getHeroImageUrl(),
            'game' => $game
        ];
        return $this->render('/visitor/games/play.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
