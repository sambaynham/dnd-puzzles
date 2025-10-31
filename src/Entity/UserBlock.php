<?php

namespace App\Entity;

use App\Repository\UserBlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBlockRepository::class)]
class UserBlock extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\OneToOne(inversedBy: 'userBlock', cascade: ['persist'])]
        #[ORM\JoinColumn(nullable: false)]
        private readonly User $user,

        #[ORM\Column(length: 255)]
        private string $reason,

        #[ORM\Column(nullable: true)]
        private ?\DateTime $expirationDate = null,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getExpirationDate(): ?\DateTime
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTime $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isPermanent(): bool {
        return $this->expirationDate === null;
    }
}

