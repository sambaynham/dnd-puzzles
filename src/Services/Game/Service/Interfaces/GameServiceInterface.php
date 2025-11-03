<?php

declare(strict_types=1);

namespace App\Services\Game\Service\Interfaces;

use App\Entity\User;
use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;

interface GameServiceInterface
{
    public function getRandomUnusedSlug(): string;

    public function findOneBySlug(string $slug): ? Game;

    public function getOutstandingInvitationsForGame(Game $game): iterable;

    public function findInvitationByCode(string $invitationCode): ? GameInvitation;

    public function findInvitationByCodeAndEmailAddress(string $invitationCode, string $emailAddress): ? GameInvitation;

    public function getExpiredInvitations(): array;

    public function getOutstandingInvitationsForUser(User $user): array;
}
