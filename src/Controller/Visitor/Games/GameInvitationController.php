<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Games;

use App\Controller\AbstractBaseController;
use App\Dto\Game\InvitePlayerDto;
use App\Entity\Game;
use App\Entity\GameInvitation;
use App\Form\InvitePlayerType;
use App\Form\RevokeInvitationType;
use App\Repository\UserRepository;
use App\Security\GameManagerVoter;
use App\Services\Puzzle\Infrastructure\CodeGenerator;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class GameInvitationController extends AbstractBaseController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private UserRepository $userRepository,
        private GameInvitationRepository $gameInvitationRepository
    ) {
    }

    /**
     * @throws RandomException
     */
    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
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
                    ->to($dto->email)
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
        return $this->render('games/invitations/invite.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(GameManagerVoter::MANAGE_GAME_ACTION, 'game')]
    #[Route('games/{slug}/invitations/{invitationCode}/revoke', name: 'app.games.invite.revoke')]
    public function redeem(
        Game $game,
        Request $request,
        string $invitationCode
    ) {

        $invitation = $this->gameInvitationRepository->findByInvitationCode($invitationCode);
        if (!$invitation) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(RevokeInvitationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->revoke();
            $this->entityManager->persist($invitation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Invitation Revoked');
            return $this->redirectToRoute('app.games.manage', ['slug' => $game->getSlug()]);
        }

        $pageVars = [
            'pageTitle' => sprintf('Really revoke %s\'s  invitation?', $invitation->getEmail()),
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
            'form' => $form
        ];
        return $this->render('games/invitations/revoke.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
