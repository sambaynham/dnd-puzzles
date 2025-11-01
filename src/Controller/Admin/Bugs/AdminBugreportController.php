<?php

declare(strict_types=1);

namespace App\Controller\Admin\Bugs;

use App\Controller\AbstractBaseController;
use App\Dto\Admin\ActionBugReportDto;
use App\Entity\BlockedEmailAddress;
use App\Entity\BugReport;
use App\Form\Admin\ActionBugReportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminBugreportController extends AbstractBaseController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    #[Route('/bugs{id}/action', name: 'admin.bugs.action')]
    #[IsGranted('manage_bugs')]
    public function action(BugReport $bugReport, Request $request): Response {

        $dto = new ActionBugReportDto($bugReport);
        $form = $this->createForm(ActionBugReportType::class, $dto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($dto->actioned === true) {
                $bugReport->markActioned();
            }
            if ($dto->close === true) {
                $bugReport->close();
            }

            if ($dto->blockSenderEmail === true) {
                $senderEmail = $bugReport->getReporterEmail();
                $blockedEmailAddress =new BlockedEmailAddress(emailAddress: $senderEmail,blockReason: 'False Bug Report');
                $this->entityManager->persist($blockedEmailAddress);
            }
            $this->entityManager->persist($bugReport);
            $this->entityManager->flush();
            $this->addFlash('success', 'Bug report actioned.');
            return $this->redirectToRoute('admin.dashboard');
        }
        $pageVars = [
            'pageTitle' => sprintf("Action bug report %d", $bugReport->getId()),
            'form' => $form
        ];
        return $this->render('admin/bugs/action.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
