<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\Static;

use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Controller\Visitor\Puzzles\PuzzleController;
use App\Dto\Visitor\Game\AddPuzzle\AddPuzzleStepOneDto;
use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookCreateDto;
use App\Form\Visitor\Puzzle\Static\Casebook\CasebookCreateFormType;
use App\Security\GameManagerVoter;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Casebook\Casebook;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CasebookPuzzleController extends AbstractPuzzleController
{
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('/puzzles/templates/static/casebook/add-to-game/{gameSlug}/create', name: 'app.puzzles.static.casebook.create')]
    public function createPuzzle(
        Game $game,
        Request $request
    ): Response {
        $session = $request->getSession();
        $sessionValues = $session->get(PuzzleController::ADD_TO_GAME_SESSION_KEY);
        $options = $this->serializer->deserialize($sessionValues, AddPuzzleStepOneDto::class, 'json');
        $dto = new CasebookCreateDto($options->puzzleName);
        $form = $this->createForm(CasebookCreateFormType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $casebook = new Casebook(
                name: $dto->puzzleName,
                slug: $this->puzzleService->generatePuzzleSlug($options->puzzleName, Casebook::class),
                game: $game,
                brief: $dto->brief
            );
            $this->entityManager->persist($casebook);
            $this->entityManager->flush();
            $this->addFlash(type: 'success', message: "Casebook created");
        }
        $pageVars = [
            'pageTitle' => sprintf("Set up Casebook puzzle '%s'", $options->puzzleName),
            'form' => $form
        ];
        return $this->render('/visitor/puzzles/templates/casebook/create.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
