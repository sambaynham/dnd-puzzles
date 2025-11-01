<?php

declare(strict_types=1);

namespace App\Controller\Visitor\Games;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\Game\Invitations\DeclineInvitationDto;
use App\Dto\Visitor\Game\Invitations\InvitationRedemptionDto;
use App\Dto\Visitor\Game\Invitations\InvitePlayerDto;
use App\Entity\AbuseReport;
use App\Entity\Game;
use App\Entity\GameInvitation;
use App\Entity\User;
use App\Form\Visitor\Game\Invitations\DeclineInvitationType;
use App\Form\Visitor\Game\Invitations\InvitePlayerType;
use App\Form\Visitor\Game\Invitations\RedeemInvitationType;
use App\Form\Visitor\Game\Invitations\RevokeInvitationType;
use App\Repository\UserRepository;
use App\Security\GameManagerVoter;
use App\Security\InvitationOwnerVoter;
use App\Services\Puzzle\Infrastructure\CodeGenerator;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
                invitationMessage: $dto->invitationText,
                game: $game,
                expiresAt: \DateTimeImmutable::createFromMutable($expiration),
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
        return $this->render('visitor/games/invitations/invite.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[IsGranted(InvitationOwnerVoter::MANAGE_INVITATION_ACTION, 'invitation')]
    #[Route('games/{slug}/invitations/{invitationCode}/revoke', name: 'app.games.invite.revoke')]
    public function revoke(
        Request $request,
        GameInvitation $invitation
    ) {
        $form = $this->createForm(RevokeInvitationType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $invitation->revoke();
            $this->entityManager->persist($invitation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Invitation Revoked');
            return $this->redirectToRoute('app.games.manage', ['slug' => $invitation->getGame()->getSlug()]);
        }

        $pageVars = [
            'pageTitle' => sprintf('Really revoke %s\'s  invitation?', $invitation->getEmail()),
            'breadcrumbs' => [

            ],
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/revoke.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('games/invitations/{invitationCode}/decline', name: 'app.games.invite.decline')]
    #[IsGranted('ROLE_USER')]
    public function decline(GameInvitation $invitation, Request $request) {
        $user = $this->getUser();


        if (!$user instanceof User || $user->getUserIdentifier() !== $invitation->getEmail()) {
            throw new AccessDeniedHttpException("You may not decline invitations on someone else\'s behalf.");
        }
        $dto = new DeclineInvitationDto($invitation);
        $form = $this->createForm(DeclineInvitationType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $invitation->revoke();
            $this->entityManager->persist($invitation);
            if ($dto->reason !== 'received_in_error') {
                $abuseReport = new AbuseReport(
                    reportedUser: $invitation->getGame()->getGamesMaster(),
                    reportingUser: $user,
                    reason: $dto->reason,
                    notes: $dto->notes,
                );
                $this->entityManager->persist($abuseReport);
            }
            $this->entityManager->flush();
            $this->addFlash('success', 'Invitation declined');
            return $this->redirectToRoute('app.user.account');
        }


        $pageVars = [
            'pageTitle' => 'Decline Invitation',
            'breadcrumbs' => [

            ],
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/decline.html.twig', $this->populatePageVars($pageVars, $request));

    }

    #[Route('games/invitations/redeem', name: 'app.games.invite.redeem')]
    public function redeem(
        Request $request
    ) {
        $dto = new InvitationRedemptionDto();
        $form = $this->createForm(RedeemInvitationType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $invitation = $this->gameInvitationRepository->findByInvitationCodeAndEmailAddress($dto->invitationCode, $dto->emailAddress);
            if (!$invitation) {
                $this->addFlash('error', 'Sorry, we couldn\'t find a current Game Invitation matching these details. Please check and try again' );
            } else {
                $user = $invitation->getUser();
                if (null === $user) {
                    $this->addFlash('success', 'Invitation found. Please register to continue: we\'ve filled in some details for you!');
                    return $this->redirectToRoute('app.auth.register', [
                        'emailAddress' => $invitation->getEmail(),
                        'invitationCode' => $invitation->getInvitationCode()
                    ]);
                }
                dd($user);
//                $invitation->markUsed();
//                $this->entityManager->persist($invitation);
                //Here, if a user does not exist, we need to create one.
            }
            /*
            $invitation->revoke();
            $this->entityManager->persist($invitation);
            $this->entityManager->flush();
            $this->addFlash('success', 'Invitation Revoked');
            return $this->redirectToRoute('app.games.manage', ['slug' => $game->getSlug()]);*/
        }

        $pageVars = [
            'pageTitle' => 'Redeem your Invitation',
            'breadcrumbs' => [

            ],
            'form' => $form
        ];
        return $this->render('visitor/games/invitations/redeem.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
