<?php

declare(strict_types=1);

namespace App\Dto\Admin\Abuse;

use App\Entity\AbuseReport;

class AbuseReportCheckDto
{
    public function __construct(
        public AbuseReport $abuseReport,
        public bool $reportConfirmed = false,
        public bool $reportReporter = false
    ) {

    }
}
