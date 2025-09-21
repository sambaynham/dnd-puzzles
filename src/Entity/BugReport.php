<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BugReportRepository;
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
}
