<?php

namespace App\EventListener;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

readonly class MaintenanceKernelRequestSubscriber
{
    public function __construct(
        #[Autowire('%env(bool:MAINTENANCE_MODE)%')]
        private bool $isMaintenance,
        private Environment $twig,
    ) {
    }

    #[AsEventListener(event: KernelEvents::REQUEST, priority: PHP_INT_MAX - 1000)]
    public function onMaintenance(RequestEvent $event): void
    {
        if (!$this->isMaintenance) {
            return;
        }

        $event->setResponse(new Response(
            $this->twig->render('maintenance/maintenance.html.twig'),
            Response::HTTP_OK
        ));
        $event->stopPropagation();
    }
}
