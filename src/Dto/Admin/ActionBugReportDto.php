<?php

declare(strict_types=1);

namespace App\Dto\Admin;

use App\Entity\BugReport;

class ActionBugReportDto
{
    public function __construct(
        public BugReport $bugReport,
        public bool $actioned = false,
        public bool $close = false,
        public bool $blockSenderEmail = false
    ) {}
}
