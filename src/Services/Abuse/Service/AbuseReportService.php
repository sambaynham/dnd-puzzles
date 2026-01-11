<?php

declare(strict_types=1);

namespace App\Services\Abuse\Service;

use App\Services\Abuse\Domain\AbuseReport;
use App\Services\Abuse\Infrastructure\AbuseReportRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

class AbuseReportService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private AbuseReportRepository $abuseReportRepository
    ) {}

    public function findUnactioned(): array
    {
        return $this->abuseReportRepository->findUnactioned();
    }

    public function getAbuseReportsForReportedUser(User $user, bool $checkedOnly = true): array {
        return $this->abuseReportRepository->getAbuseReportsForReportedUser($user, $checkedOnly);
    }

    public function getAbuseReportsByUser(User $user): array {

        return $this->abuseReportRepository->getAbuseReportsByUser($user);
    }

    public function deleteReport(AbuseReport $report): void {
        $this->entityManager->remove($report);
    }

    public function removeUserAssociation(AbuseReport $report, User $user): void {
        $report->
        dd($report);
    }
}
