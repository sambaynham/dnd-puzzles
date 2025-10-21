<?php

namespace App\Controller\Visitor;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Controller\AbstractBaseController;
use App\Dto\Game\CreateGameDto;
use App\Dto\Game\InvitePlayerDto;
use App\Entity\Game;
use App\Entity\GameInvitation;
use App\Entity\User;
use App\Form\CreateGameType;
use App\Form\InvitePlayerType;
use App\Repository\GameRepository;
use App\Repository\UserRepository;
use App\Services\Puzzle\Infrastructure\CodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class GamesController extends AbstractBaseController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly  GameRepository $gameRepository,
        private MailerInterface $mailer,
        private UserRepository $userRepository
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
        return $this->render('games/create.html.twig', $this->populatePageVars($pageVars, $request));
    }


    #[IsGranted('ROLE_USER')]
    #[Route('games/{slug}/manage/', name: 'app.games.manage')]
    public function manage(
        Game $game,
        Request $request
    ) {
        $title = sprintf('Manage %s', $game->getName());
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
        ];
        return $this->render('games/manage.html.twig', $this->populatePageVars($pageVars, $request));
    }

    /**
     * @throws RandomException
     */
    #[IsGranted('ROLE_USER')]
    #[Route('games/{slug}/manage/invitations', name: 'app.games.invite')]
    public function invite(
        Game $game,
        Request $request
    ) {

        $dto = new InvitePlayerDto();
        $dto->game = $game;
        $dto->invitationCode = CodeGenerator::generateRandomCode(8);
        $dto->invitationText = 'Hi, I\'d like you to join my game on conundrumcodex.com';
        $form = $this->createForm(InvitePlayerType::class, $dto );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $dto->email;
            $expiration = new \DateTime();
            $expiration->modify('+24 hours');
            $existingUser = $this->userRepository->findOneBy(['email' => $email]);
            $invitation = new GameInvitation(
                invitationCode: $dto->invitationCode,
                email: $email,
                game: $game,
                expiresAt:  \DateTimeImmutable::createFromMutable($expiration),
            );
            if ($existingUser) {
                $invitation->setUser($existingUser);
            }
            $this->entityManager->persist($invitation);
            $this->entityManager->flush();

            if (null === $existingUser) {
                $email = (new TemplatedEmail())
                    ->from(new Address('site@conundrumcodex.com', 'Mailbot'))
                    ->to((string) $existingUser)
                    ->subject(sprintf('Invitation from %s', $game->getGamesMaster()->getEmail()))
                    ->htmlTemplate('mail/invitation.html.twig')
                    ->context([
                        'invitation' => $invitation,
                    ]);

                $this->mailer->send($email);
            }
            $this->addFlash('success', 'Invitation Sent');
            return $this->redirectToRoute('app.games.manage', ['slug' => $game->getSlug()]);

        }
        $pageVars = [
            'pageTitle' => sprintf('Invite player to %s', $game->getName()),
            'breadcrumbs' => [
                [
                    'route' => 'app.games.index',
                    'label' => 'My Games',
                    'active' => false
                ],
                [
                    'label' => sprintf('Manage %s', $game->getName()),
                    'active' => false
                ],
                [
                    'label' => 'Invite Players',
                    'active' => true
                ],
            ],
            'game' => $game,
            'form' => $form
        ];
        return $this->render('games/invite.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
