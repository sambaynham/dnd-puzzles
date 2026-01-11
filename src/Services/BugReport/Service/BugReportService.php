<?php

declare(strict_types=1);

namespace App\Services\BugReport\Service;

use App\Services\BugReport\Domain\BugReport;
use App\Services\BugReport\Infrastructure\BugReportRepository;

class BugReportService
{
    public function __construct(private BugReportRepository $bugReportRepository) {}

    /**
     * @return BugReport[]
     */
    public function findUnactioned(): array {
        return $this->bugReportRepository->findUnactioned();
    }
}
