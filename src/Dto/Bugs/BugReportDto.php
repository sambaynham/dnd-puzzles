<?php

declare(strict_types=1);

namespace App\Dto\Bugs;

class BugReportDto
{
    public ? string $summary = null;

    public ? string $reporterName = null;

    public ?string $reporterEmail = null;

    public ?string $text = null;

    public ?string $referringUrl = null;
}
