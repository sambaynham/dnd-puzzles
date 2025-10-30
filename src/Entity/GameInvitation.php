<?php

namespace App\Entity;

use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameInvitationRepository::class)]
class GameInvitation extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 64)]
        private string $invitationCode,

        #[ORM\Column(type: 'string')]
        private string $email,

        #[ORM\ManyToOne(inversedBy: 'gameInvitations')]
        #[ORM\JoinColumn(nullable: false)]
        private Game $game,

        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $expiresAt,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: true)]
        private ?User $user = null,

        #[ORM\Column(type: 'date_immutable', nullable: true)]
        private ?\DateTimeImmutable $dateUsed = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function markUsed(): void {
        $this->dateUsed = new \DateTimeImmutable();
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getInvitationCode(): string
    {
        return $this->invitationCode;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getExpiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isUsed(): bool {
        return null !== $this->dateUsed;
    }

    public function revoke(): void {
        $this->expiresAt = new \DateTimeImmutable();
    }
}
