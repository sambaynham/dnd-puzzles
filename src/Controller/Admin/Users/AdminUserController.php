<?php

declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Controller\AbstractBaseController;
use App\Dto\Admin\User\AdminUserDto;
use App\Dto\Visitor\User\UserBlockDto;
use App\Form\Admin\AdminBlockUserType;
use App\Form\Admin\AdminUnblockUserType;
use App\Form\Admin\AdminUserEditType;
use App\Form\Admin\AdminUserSearchType;
use App\Services\Quotation\Service\QuotationService;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserBlock;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminUserController extends AbstractBaseController
{
    public function __construct(
        private readonly UserServiceInterface $userService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly ValidatorInterface $validator,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    #[Route('admin/users', name: 'admin.users.manage')]
    #[IsGranted('manage_users')]
    public function index(
        Request $request,
        #[MapQueryParameter] ?int $page = 1,
        #[MapQueryParameter] ?int $resultsPerPage = 10,
    ): Response {
        $form = $this->createForm(AdminUserSearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $results = $this->userService->findByEmailOrUserName(searchTerms: $data['search'], firstResult: ($resultsPerPage * ($page - 1)),maxResults: $resultsPerPage);
        } else {
            $results = $this->userService->getUsersPaginated(firstResult: ($resultsPerPage * ($page-1)), maxResults: $resultsPerPage);
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
    #[IsGranted('manage_users')]
    public function edit(User $user, Request $request): Response {
        $currentUser = $this->getUser();
        if ($user === $currentUser) {
            return $this->redirectToRoute('app.user.account');
        }
        $dto = AdminUserDto::makeFromUser($user);
        $form = $this->createForm(AdminUserEditType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEmail($dto->email);
            $user->setUsername($dto->username);
            $user->setRoles($dto->roles);

            if (null !== $dto->plainPassword) {
                $user->setPassword($this->userPasswordHasher->hashPassword($user, $dto->plainPassword));
            }
            $success = true;
            $violations = $this->userService->validateUser($user);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }

            if ($success) {
                $this->userService->saveUser($user);
                $this->addFlash('success', 'User changes saved successfully.');
                return $this->redirectToRoute('admin.users.manage');
            }
        }
        $pageVars = [
            'pageTitle' => sprintf('Edit user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/{id}/block', name: 'admin.users.block')]
    #[IsGranted('block_users')]
    public function blockUser(User $user, Request $request): Response {
        $currentUser = $this->getUser();
        if ($user === $currentUser) {
            $this->addFlash('error', 'You can\'t block yourself!');
            return $this->redirectToRoute('admin.users.manage');
        }

        $blockDto = new UserBlockDto($user);
        $form = $this->createForm(AdminBlockUserType::class, $blockDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userBlock = new UserBlock(
                $user,
                $blockDto->reason,
                $blockDto->expiresAt
            );
            $success = true;
            $violations = $this->validator->validate($userBlock);
            if (count($violations) > 0) {
                $success = false;
                foreach ($violations as $violation) {
                    $this->addFlash('error', $violation->getMessage());
                }
            }
            if ($success) {
                $this->entityManager->persist($userBlock);
                $this->entityManager->flush();
                $this->addFlash('success', 'User blocked successfully.');
                return $this->redirectToRoute('admin.users.manage');
            }
        }
        $pageVars = [
            'pageTitle' => sprintf('Block user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/block.html.twig', $this->populatePageVars($pageVars, $request));

    }

    #[Route('admin/users/{id}/unblock', name: 'admin.users.unblock')]
    #[IsGranted('block_users')]
    public function unblockUser(User $user, Request $request): Response {


        $blockDto = new UserBlockDto($user);
        $form = $this->createForm(AdminUnblockUserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userBlock = $user->getUserBlock();


            $this->entityManager->remove($userBlock);
            $this->entityManager->flush();
            $this->addFlash('success', 'User unblocked successfully.');
            return $this->redirectToRoute('admin.users.manage');

        }
        $pageVars = [
            'pageTitle' => sprintf('Unblock user %s', $user->getUsername()),
            'user' => $user,
            'form' => $form
        ];
        return $this->render('admin/users/block.html.twig', $this->populatePageVars($pageVars, $request));

    }

    private function generatePager(int $page, int $resultsPerPage): array {
        $usersCount = $this->userService->getUsersCount();
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
