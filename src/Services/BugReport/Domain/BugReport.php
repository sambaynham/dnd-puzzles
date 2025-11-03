<?php

declare(strict_types=1);

namespace App\Services\BugReport\Domain;

use App\Entity\AbstractDomainEntity;
use App\Services\BugReport\Infrastructure\BugReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BugReportRepository::class)]
class BugReport extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        private string $summary,

        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        private string $reporterName,

        #[ORM\Column(length: 255)]
        #[Assert\NotBlank]
        #[Assert\Email]
        private string $reporterEmail,

        #[Assert\NotBlank]
        #[ORM\Column(type: Types::TEXT)]
        private string $text,

        #[ORM\Column(length: 255, nullable: true)]
        #[Assert\Url]
        private ? string $referringUrl = null,

        #[ORM\Column(length: 255, nullable: true, type: Types::DATETIME_IMMUTABLE)]
        private ? \DateTimeImmutable $actionedAt = null,

        #[ORM\Column(length: 255, nullable: true, type: Types::DATETIME_IMMUTABLE)]
        private ? \DateTimeImmutable $closedAt = null,

        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getReferringUrl(): ?string
    {
        return $this->referringUrl;
    }

    public function setReferringUrl(?string $referringUrl): void
    {
        $this->referringUrl = $referringUrl;
    }

    public function getReporterEmail(): string
    {
        return $this->reporterEmail;
    }

    public function setReporterEmail(string $reporterEmail): void
    {
        $this->reporterEmail = $reporterEmail;
    }

    public function getReporterName(): string
    {
        return $this->reporterName;
    }

    public function setReporterName(string $reporterName): void
    {
        $this->reporterName = $reporterName;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function markActioned(): void {
        $this->actionedAt = new \DateTimeImmutable();
    }

    public function getActionedAt(): \DateTimeImmutable {
        return $this->actionedAt;
    }

    public function isActioned(): bool {
        return $this->actionedAt !== null;
    }

    public function isClosed(): bool {
        return $this->closedAt !== null;
    }

    public function getClosedAt(): ?\DateTimeImmutable {
        return $this->closedAt;
    }

    public function close(): void {
        $this->closedAt = new \DateTimeImmutable();
    }
}
