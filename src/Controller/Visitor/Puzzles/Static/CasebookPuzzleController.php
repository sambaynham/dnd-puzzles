<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Puzzles\Static;

use App\Controller\Visitor\Puzzles\AbstractPuzzleController;
use App\Controller\Visitor\Puzzles\PuzzleTemplateController;
use App\Dto\Visitor\Game\AddPuzzle\AddPuzzleStepOneDto;
use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookCreateDto;
use App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectDto;
use App\Form\Visitor\Puzzle\Static\Casebook\CasebookCreateFormType;
use App\Form\Visitor\Puzzle\Static\Casebook\CasebookSubjectType;
use App\Security\GameManagerVoter;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Domain\Casebook\CasebookSubject;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClue;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectClueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
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

            return $this->redirectToRoute('app.puzzles.static.casebook.edit', [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => Casebook::TEMPLATE_SLUG,
                'instanceCode' => $casebook->getInstanceCode(),
            ]);
        }
        $pageVars = [
            'pageTitle' => sprintf("Set up Casebook puzzle '%s'", $options->puzzleName),
            'form' => $form
        ];
        return $this->render('/visitor/puzzles/templates/casebook/create.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/add', name: 'app.puzzles.static.casebook.subjects.add')]
    public function addSubject(
        Game $game,
        PuzzleInstanceInterface $puzzleInstance,
        Request $request,
        #[Autowire('%kernel.project_dir%/public/uploads/images')] string $publicImagesDirectory
    ): Response {
        if (!$puzzleInstance instanceof  Casebook) {
            throw new BadRequestException();
        }

        $dto = new CasebookSubjectDto(casebook: $puzzleInstance);

        $form = $this->createForm(CasebookSubjectType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $this->slugger->slug($originalFilename);
                $imageFileName = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();
                try {
                    $imageFile->move($publicImagesDirectory, $imageFileName);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
            } else {
                $imageFileName = null;
            }
            $clues = new ArrayCollection();
            $subject = new CasebookSubject(
                name: $dto->name,
                description: $dto->description,
                casebook: $puzzleInstance,
                casebookSubjectClues: $clues,
                casebookSubjectNotes: new ArrayCollection(),
                casebookSubjectImage: $imageFileName,
                casebookSubjectType: $dto->type
            );

            foreach ($dto->clues as $clueEntry) {
                if (!$clueEntry->isBlank()) {
                    $clues->add(new CasebookSubjectClue(
                        title: $clueEntry->title,
                        body: $clueEntry->body,
                        type: $clueEntry->type,
                        casebookSubject: $subject
                    ));
                }


            }

            $this->entityManager->persist($subject);
            $this->entityManager->flush();
        }

        $pageVars = [
            'pageTitle' => sprintf("Add a subject to  '%s'", $puzzleInstance->getName()),
            'form' => $form,
            'game' => $game,
        ];
        return $this->render('/visitor/puzzles/templates/casebook/subjects/add.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/{subjectId}/edit', name: 'app.puzzles.static.casebook.subjects.edit')]
    public function editSubject(
        Game $game,
        PuzzleInstanceInterface $puzzleInstance,
        PuzzleTemplate $puzzleTemplate,
        CasebookSubject $subject,
        CasebookSubjectClueRepository $casebookSubjectClueRepository,
        Request $request,
        #[Autowire('%kernel.project_dir%/public/uploads/images')] string $publicImagesDirectory
    ): Response {
        if (!$puzzleInstance instanceof  Casebook) {
            throw new BadRequestException();
        }

        $dto = CasebookSubjectDto::makeFromCasebookSubject($subject);
        $form = $this->createForm(CasebookSubjectType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $subject->setName($dto->name);
            $subject->setDescription($dto->description);
            foreach ($dto->clues as $clueEntry) {
                if ($clueEntry->id !== null) {
                    $existingClue = $casebookSubjectClueRepository->find($clueEntry->id);
                    $existingClue->setTitle($clueEntry->title);
                    $existingClue->setBody($clueEntry->body);
                    $existingClue->setType($clueEntry->type);
                    $this->entityManager->persist($existingClue);
                } else {
                    if (!$clueEntry->isBlank()) {
                        $newClue = new CasebookSubjectClue(
                            title: $clueEntry->title,
                            body: $clueEntry->body,
                            type: $clueEntry->type,
                            casebookSubject: $subject
                        );
                        $this->entityManager->persist($newClue);
                    }

                }
            }

            $this->entityManager->persist($subject);
            $this->entityManager->flush();
            $this->addFlash('success', sprintf("%s '%s' edited.", (string) $subject->getCasebookSubjectType(), $subject->getName()));
            return $this->redirectToRoute('app.puzzles.static.casebook.edit', [
                'gameSlug' => $game->getSlug(),
                'templateSlug' => $puzzleTemplate->getSlug(),
                'instanceCode' => $puzzleInstance->getInstanceCode(),
            ]);
        }
        $pageVars =[
            'pageTitle' => sprintf('Edit subject "%s"', $subject->getName()),
            'form' => $form,
            'subject' => $subject,
            'game' => $game,
            'template' => $puzzleTemplate,
            'instance' => $puzzleInstance
        ];
        return $this->render('/visitor/puzzles/templates/casebook/subjects/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route(
        path: 'games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/subjects/{subjectId}/clues/{clueId}/reveal',
        name: 'app.puzzles.static.casebook.subjects.clues.reveal',
        methods: ['POST', 'GET']
    )]
    public function revealClue(
        Game $game,
        PuzzleInstanceInterface $puzzleInstance,
        PuzzleTemplate $puzzleTemplate,
        CasebookSubject $subject,
        CasebookSubjectClue $clue,
        Request $request,
    ): Response {
        if (!$puzzleInstance instanceof Casebook) {
            return new JsonResponse([
                'message' => 'Not a Casebook Instance'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        if (null !== $clue->getRevealedDate()) {
            return new JsonResponse([
                'message' => 'Clue already revealed'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $clue->reveal();
        $this->entityManager->persist($clue);
        $this->entityManager->flush();
        return new JsonResponse([], Response::HTTP_OK);
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{gameSlug}/puzzles/static/{templateSlug}/{instanceCode}/edit', name: 'app.puzzles.static.casebook.edit')]
    public function editInstance(
        Game $game,
        PuzzleTemplate $template,
        PuzzleInstanceInterface $instance,
        Request $request
    ): Response {
        $pageVars = [
            'pageTitle' => sprintf("Edit Casebook puzzle '%s'", $instance->getName()),
            'casebook' => $instance,
            'game' => $game,
            'template' => $template,
        ];
        return $this->render('/visitor/puzzles/templates/casebook/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

}
