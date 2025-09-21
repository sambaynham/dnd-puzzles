<?php

declare(strict_types=1);

namespace App\Controller\Visitor;

use App\Controller\AbstractBaseController;
use App\Dto\Bugs\BugReportDto;
use App\Entity\BugReport;
use App\Form\BugReportType;
use App\Message\EmailMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class BugReportController extends AbstractBaseController
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageBusInterface $bus
    ) {}

    /**
     * @throws ExceptionInterface
     */
    #[Route('/bugs/report', name: 'app.bugs.report')]
    public function report(Request $request): Response {
        $dto = new BugReportDTO();
        $referringUri = $request->query->get('referringUri');
        if (null !== $referringUri) {
            $dto->referringUrl = $referringUri;

        }
        $form = $this->createForm(BugReportType::class, $dto);



        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bugReport = new BugReport(
                summary: $dto->summary,
                reporterName: $dto->reporterName,
                reporterEmail: $dto->reporterEmail,
                text: $dto->text,
                referringUrl: strip_tags($dto->referringUrl),
            );
            $this->entityManager->persist($bugReport);
            $this->entityManager->flush();
            $this->bus->dispatch(new EmailMessage(
                fromEmail: 'sender@conundrumcodex.com',
                toEmail: 'sam@sam-baynham.dev',
                subject: sprintf("New bug reported: %s", $bugReport->getSummary()),
                body: $bugReport->getText()
            ));
            /*
            $email = (new Email())
                ->from('sender@conundrumcodex.com')
                ->to('sam@sam-baynham.dev')
                ->subject()
                ->text($bugReport->getText());
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                //->priority(Email::PRIORITY_HIGH)

            $email->getHeaders()->addTextHeader('AhaSend-Sandbox', 'true');



            $this->mailer->send($email);*/
            $this->addFlash('success', 'Your bug report has been sent. Someone will examine this as soon as possible. Thank you for your patience.');
            return $referringUri ? $this->redirect($referringUri) : $this->redirectToRoute('app.pages.home');
        }
        $pageVars =[
            'pageTitle' => 'Report a bug',
            'form' => $form,
            'hideBugReportLink' => true
        ];
        return $this->render('bugs/report.html.twig', $this->populatePageVars($pageVars, $request));
    }
}
