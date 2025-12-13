<?php

declare(strict_types=1);

namespace App\Services\Abuse\Domain;

use App\Services\Abuse\Infrastructure\AbuseReportRepository;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AbuseReportRepository::class)]
class AbuseReport extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User $reportedUser,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User $reportingUser,

        #[ORM\Column(length: 1024)]
        private string $reason,

        #[ORM\Column(length: 1024)]
        private ? string $notes = null,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
        private ?\DateTimeImmutable $checkedDate = null,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
        private ?\DateTimeImmutable $confirmedDate = null,

        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getConfirmedDate(): ?\DateTimeImmutable
    {
        return $this->confirmedDate;
    }

    public function getReportedUser(): User
    {
        return $this->reportedUser;
    }

    public function setReportedUser(User $reportedUser): void
    {
        $this->reportedUser = $reportedUser;
    }

    public function getReportingUser(): User
    {
        return $this->reportingUser;
    }

    public function setReportingUser(User $reportingUser): void
    {
        $this->reportingUser = $reportingUser;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getCheckedDate(): ?\DateTimeImmutable
    {
        return $this->checkedDate;
    }

    public function markChecked(): void {
        $this->checkedDate = new \DateTimeImmutable();
    }

    public function markConfirmed(): void {
        $this->confirmedDate = new \DateTimeImmutable();
    }

}
