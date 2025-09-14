<?php

namespace App\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Game\CreateGameDto;
use App\Entity\Game;
use App\Entity\User;
use App\Form\CreateGameType;
use App\ValueResolver\GameSlugResolver;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
final class GamesController extends AbstractBaseController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator
    ) {

    }

    #[Route('/games', name: 'app.games.index')]
    public function index(Request $request): Response
    {
        $pageVars = [
            'pageTitle' => 'My Games',
        ];
        return $this->render('games/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/create', name: 'app.games.create')]
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
            $violations = [];
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
            }

        }
        $pageVars = [
            'pageTitle' => 'Create a Game',
            'form' => $form
        ];
        return $this->render('games/create.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/games/{slug}/play', name: 'app.games.manage')]
    public function manage(
        #[MapEntity(class: Game::class,resolver: GameSlugResolver::class)]
        Game $game,
        Request $request
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to manage a game.');
        }
        if ($game->getGamesMaster() !== $user) {
            throw $this->createAccessDeniedException('You are not the games master of this game.');
        }
        $pageVars = [
            'pageTitle' => sprintf("Manage %s", $game->getName()),
            'game' => $game,
        ];
        return $this->render('games/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/games/{slug}/play', name: 'app.games.play')]
    public function play(
        #[MapEntity(class: Game::class,resolver: GameSlugResolver::class)]
        Game $game,
        Request $request
    ): Response {
        $pageVars = [
            'pageTitle' => 'Create a Game',
        ];
        return $this->render('games/play.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
