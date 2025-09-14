<?php

namespace App\Controller;

use ApiPlatform\Validator\Exception\ValidationException;
use ApiPlatform\Validator\ValidatorInterface;
use App\Dto\UserDto;
use App\Entity\User;
use App\Form\RegistrationForm;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractBaseController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private ValidatorInterface $validator
    )
    {
    }

    #[Route('/register', name: 'app.user.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {

        $userDto = new UserDto();
        $form = $this->createForm(RegistrationForm::class, $userDto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user = new User(
                $userDto->userName,
                $userDto->emailAddress,
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

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app.user.register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app.user.register');
    }
}
