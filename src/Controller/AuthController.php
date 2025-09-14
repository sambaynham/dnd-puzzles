<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractBaseController
{

    public function __construct(private readonly AuthenticationUtils $authenticationUtils) {
    }

    #[Route('/login', name: 'app.auth.login')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LoginType::class);

        $error = $this->authenticationUtils->getLastAuthenticationError();
        if ($error) {
            dd($error->getMessageKey());
        }

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
