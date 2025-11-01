<?php

declare(strict_types=1);

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Dto\Visitor\User\UserChangePasswordDto;
use App\Dto\Visitor\User\UserEditDto;
use App\Entity\User;
use App\Form\Visitor\Account\ChangePasswordType;
use App\Form\Visitor\Account\UserEditType;
use App\Form\Visitor\Game\JoinGameType;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class AccountController extends AbstractBaseController
{
    public function __construct(
        private readonly GameInvitationRepository $gameInvitationRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    #[Route('/account', name: 'app.user.account')]
    public function index(Request $request): Response
    {

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        $joinGameForm = $this->createForm(JoinGameType::class);
        $pageVars = [
            'pageTitle' => 'Account',
            'user' => $user,
            'invitations' => $this->gameInvitationRepository->getOutstandingInvitationsForUser($user)
        ];
        return $this->render('/visitor/account/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/account/change-details', name: 'app.user.account.edit')]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        $dto = UserEditDto::makeFromUser($user);
        $form = $this->createForm(UserEditType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEmail($dto->emailAddress);
            $user->setUsername($dto->userName);
            $success = true;
            $violations = $this->validator->validate($dto);
            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $success = false;
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $this->addFlash('success', 'Your account has been edited.');
                return $this->redirectToRoute('app.user.account');
            }
        }
        $pageVars = [
            'pageTitle' => 'Change my Details',
            'form' => $form,
        ];
        return $this->render('/visitor/account/change-details.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/account/change-password', name: 'app.user.account.change-password')]
    public function changePassword(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('This user does not have access to this section.');
        }
        $dto = new UserChangePasswordDto();
        $form = $this->createForm(ChangePasswordType::class, $dto);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->newPassword));
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Your password has been changed.');
            return $this->redirectToRoute('app.user.account');
        }
        $pageVars = [
            'pageTitle' => 'Change my Password',
            'form' => $form,
        ];
        return $this->render('/visitor/account/change-details.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
