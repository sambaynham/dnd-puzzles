<?php

declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Controller\AbstractBaseController;
use App\Entity\User;
use App\Form\AdminUserSearchType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminUserController extends AbstractBaseController
{
    public function __construct(private UserRepository $userRepository) {
    }

    #[Route('admin/users', name: 'admin.users.manage')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(
        Request $request,
        #[MapQueryParameter] ?int $page = 1,
        #[MapQueryParameter] ?int $resultsPerPage = 10,
    ): Response {
        $form = $this->createForm(AdminUserSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $results = $this->userRepository->searchByEmailOrUserName(searchTerms: $data['search'], firstResult: ($resultsPerPage * ($page - 1)),maxResults: $resultsPerPage);
        } else {
            $results = $this->userRepository->findAllPaginated(firstResult: ($resultsPerPage * ($page-1)), maxResults: $resultsPerPage);
        }

        $pageVars = [
            'pageTitle' => 'Manage Users',
            'form' => $form,
            'resultsPerPage' => $resultsPerPage,
            'pager' => $this->generatePager($page, $resultsPerPage),
            'currentPage' => $page,
            'users' => $results,
        ];
        return $this->render('admin/users/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/{id}/edit', name: 'admin.users.edit')]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(User $user, Request $request): Response {
        $currentUser = $this->getUser();
        if ($user === $currentUser) {

            return $this->redirectToRoute('admin.users.manage');
        }
        $pageVars = [
            'pageTitle' => sprintf('Edit user %s', $user->getUsername()),
            'user' => $user,
        ];
        return $this->render('admin/users/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }
    private function generatePager(int $page, int $resultsPerPage): array {
        $usersCount = $this->userRepository->getUsersCount();
        $numPagesToGenerate = (int) ceil($usersCount / $resultsPerPage);
        $pager = [];
        $pager['pages'] =[];
        if ($numPagesToGenerate > 1) {
            if ($page > 1) {
                $pager['back'] = $page -1;
            }
            for ($i = 1; $i <= $numPagesToGenerate; $i++) {
                $pager['pages'][$i] = $i;
            }
            if ($numPagesToGenerate > $page) {
                $pager['next'] = $page +1;
            }
        }

        return $pager;
    }
}
