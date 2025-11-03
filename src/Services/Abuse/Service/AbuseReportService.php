<?php

declare(strict_types=1);

namespace App\Services\Abuse\Service;

use App\Services\Abuse\Infrastructure\AbuseReportRepository;
use App\Services\User\Domain\User;

class AbuseReportService
{
    public function __construct(private AbuseReportRepository $abuseReportRepository) {}

    public function findUnactioned(): array
    {
        return $this->abuseReportRepository->findUnactioned();
    }

    public function getAbuseReportsForReportedUser(User $user): array {
        return $this->abuseReportRepository->getAbuseReportsForReportedUser($user);
    }
}
