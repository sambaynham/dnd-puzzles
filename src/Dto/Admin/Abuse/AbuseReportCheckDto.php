<?php

declare(strict_types=1);

namespace App\Dto\Admin\Abuse;

use App\Services\Abuse\Domain\AbuseReport;

class AbuseReportCheckDto
{
    public function __construct(
        public AbuseReport $abuseReport,
        public bool $reportConfirmed = false,
        public bool $reportReporter = false
    ) {

    }
}
