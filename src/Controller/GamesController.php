<?php

namespace App\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\Game\CreateGameDto;
use App\Entity\Game;
use App\Entity\User;
use App\Form\CreateGameType;
use App\Repository\GameRepository;
use App\ValueResolver\GameSlugResolver;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class GamesController extends AbstractBaseController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly  GameRepository $gameRepository
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
        return $this->render('games/index.html.twig', $this->populatePageVars($pageVars, $request));
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
//                $this->redirectToRoute('app.games.manage', ['slug' => $game->getSlug()]);
            }

        }
        $pageVars = [
            'pageTitle' => 'Create a Game',
            'form' => $form
        ];
        return $this->render('games/create.html.twig', $this->populatePageVars($pageVars, $request));
    }


    #[IsGranted('ROLE_USER')]
    #[Route('games/{slug}/manage/', name: 'app.games.manage')]
    public function manage(
        string $slug,
        Request $request
    ) {
    }
}
