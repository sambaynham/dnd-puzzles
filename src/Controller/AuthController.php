<?php

namespace App\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
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
        private readonly AuthenticationUtils $authenticationUtils) {
    }

    #[Route('/register', name: 'app.auth.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {

        $userDto = new UserDto();
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

            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $success = true;
            try {
                $this->validator->validate($user);
            } catch (ValidationException $e) {
                $success = false;

                $this->addFlash('error', $e->getMessage());

            }

            if ($success) {
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Your account has been created. You may now log in.');
                return $this->redirectToRoute('app.auth.login');
            }

        }
        $pageVars = [
            'pageTitle' => 'Register',
            'registrationForm' => $form,
        ];

        return $this->render('registration/register.html.twig', $this->populatePageVars($pageVars, $request));
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
        ];

        return $this->render('security/login.html.twig', $this->populatePageVars(request: $request, pageVars: $pageVars));
    }

    #[Route(path: '/logout', name: 'app.auth.logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
