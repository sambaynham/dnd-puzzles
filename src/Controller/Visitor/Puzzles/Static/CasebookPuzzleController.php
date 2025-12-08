<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\Static;

use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Controller\Visitor\Puzzles\PuzzleTemplateController;
use App\Dto\Visitor\Game\AddPuzzle\AddPuzzleStepOneDto;
use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookCreateDto;
use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectDto;
use App\Form\Visitor\Puzzle\Static\Casebook\CasebookAddSubjectType;
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
        $sessionValues = $session->get(PuzzleTemplateController::ADD_TO_GAME_SESSION_KEY);
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
            return $this->redirectToRoute('app.puzzles.static.casebook.manage', ['gameSlug' => $game->getSlug(), 'casebookSlug' => $casebook->getSlug()]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Set up Casebook puzzle '%s'", $options->puzzleName),
            'form' => $form
        ];
        return $this->render('/visitor/puzzles/templates/casebook/create.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/casebook/{casebookSlug}/manage', name: 'app.puzzles.static.casebook.manage')]
    public function manageCasebook(
        Game $game,
        Casebook $casebook,
        Request $request
    ): Response {

        $pageVars = [
            'pageTitle' => sprintf("Manage Casebook puzzle '%s'", $casebook->getName()),
            'casebook' => $casebook
        ];
        return $this->render('/visitor/puzzles/templates/casebook/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/casebook/{casebookSlug}/manage/subjects/add', name: 'app.puzzles.static.casebook.subjects.add')]
    public function addSubject(
        Game $game,
        Casebook $casebook,
        Request $request
    ): Response {

        $dto = new CasebookSubjectDto(casebook: $casebook);

        $form = $this->createForm(CasebookAddSubjectType::class, $dto);
        $pageVars = [
            'pageTitle' => sprintf("Add a subject to  '%s'", $casebook->getName()),
            'form' => $form
        ];
        return $this->render('/visitor/puzzles/templates/casebook/subjects/add.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
