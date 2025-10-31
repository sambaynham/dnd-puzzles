<?php

namespace App\Controller\Visitor;

use ApiPlatform\Validator\Exception\ValidationException;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Controller\AbstractBaseController;
use App\Dto\User\UserDto;
use App\Entity\User;
use App\Form\LoginType;
use App\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractBaseController
{

    public function __construct(
        private ValidatorInterface $validator,
        private readonly AuthenticationUtils $authenticationUtils,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private Security $security,
        private EntityManagerInterface $entityManager,
        private GameInvitationRepository $gameInvitationRepository
    ) {
    }

    #[Route('/register', name: 'app.auth.register')]
    public function register(
        Request $request,
        #[MapQueryParameter] ?string $emailAddress = null,
        #[MapQueryParameter] ?string $invitationCode = null,
    ): Response{


        $userDto = new UserDto();
        if (null !== $emailAddress) {
            $userDto->emailAddress = $emailAddress;
        }
        if (null !== $invitationCode) {
            $userDto->invitationCode = $invitationCode;
        }
        $form = $this->createForm(RegistrationForm::class, $userDto);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user = new User(
                email: $userDto->emailAddress,
                password: '',
                username: $userDto->userName
            );

            $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

            $success = true;
            $violations = $this->validator->validate($user);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }


            if (null !== $userDto->invitationCode) {
                $invitation = $this->gameInvitationRepository->findByInvitationCodeAndEmailAddress(invitationCode: $userDto->invitationCode, emailAddress: $userDto->emailAddress);
                if (null === $invitation) {
                    $success = false;
                    $this->addFlash('error', 'We couldn\'t find an invitation matching those details. Please check and try again.');
                }
            } else {
                $invitation = null;
            }


            if ($success) {
                $this->entityManager->persist($user);

                $hasInvitation = false;
                if (null !== $invitation) {
                    $hasInvitation = true;
                    $invitation->markUsed();
                    $this->entityManager->persist($invitation);
                    $game = $invitation->getGame();
                    $game->addPlayer($user);
                    $this->entityManager->persist($game);
                }
                $this->entityManager->flush();

                $this->addFlash('success', $hasInvitation ? 'Your account has been created, and you\'ve been added to your game!' : 'Your account has been created. You may now log in.');
                return $this->redirectToRoute('app.auth.login');
            }

        }
        $pageVars = [
            'pageTitle' => 'Log In or Register',
            'registrationForm' => $form,
            'breadcrumbs' => [
                [
                    'route' => 'app.auth.register',
                    'label' => 'Register',
                    'active' => false
                ]
            ]
        ];

        return $this->render('/visitor/registration/register.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('/login', name: 'app.auth.login', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);

        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        $pageVars = [
            'pageTitle' => 'Login',
            'form' => $form,
            'error' => $error,
            'last_username' => $lastUsername,
            'breadcrumbs' => [
                [
                    'route' => 'app.auth.login',
                    'label' => 'Login',
                    'active' => false
                ]
            ]
        ];

        return $this->render('/visitor/security/login.html.twig', $this->populatePageVars(request: $request, pageVars: $pageVars));
    }

    #[Route(path: '/logout', name: 'app.auth.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
