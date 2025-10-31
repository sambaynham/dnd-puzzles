<?php

declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Controller\AbstractBaseController;
use App\Entity\Role;
use App\Form\Admin\RoleEditType;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminRolesController extends AbstractBaseController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RoleRepository $roleRepository
    ) {
    }

    #[Route('admin/users/roles', name: 'admin.users.roles')]
    #[IsGranted('assign_roles')]
    public function index(Request $request): Response {
        $pageVars = [
            'pageTitle' => 'Manage Roles',
            'roles' => $this->roleRepository->findAll()
        ];
        return $this->render('admin/users/roles/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/users/roles/{roleHandle}/edit', name: 'admin.users.roles.edit')]
    #[IsGranted('assign_roles')]
    public function edit(
        Role $role,
        Request $request
    ): Response {
        $form = $this->createForm(RoleEditType::class, $role);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($role);
            $this->entityManager->flush();
            $this->addFlash('success', 'Changes saved succesfully');
            return $this->redirectToRoute('admin.users.roles');
        }

        $pageVars = [
            'pageTitle' => sprintf("Edit role %s", $role->getName()),
            'role' => $role,
            'form' => $form
        ];
        return $this->render('admin/users/roles/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
