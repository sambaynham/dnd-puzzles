<?php

declare(strict_types=1);

namespace App\Services\Game\Service;

use App\Entity\User;
use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\Game\Infrastructure\GameInvitationRepository;
use App\Services\Game\Infrastructure\GameRepository;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use App\Services\Puzzle\Infrastructure\CodeGenerator;

class GameService implements GameServiceInterface
{
    public function __construct(
        private GameRepository $gameRepository,
        private GameInvitationRepository $gameInvitationRepository
    ) {}

    public function getRandomUnusedSlug(): string {

        $randomSlug = CodeGenerator::generateRandomCode();
        if (null !== $this->gameRepository->findOneBySlug($randomSlug)) {
            return $this->getRandomUnusedSlug();
        }
        return $randomSlug;
    }

    public function findOneBySlug(string $slug): ? Game {
        return $this->gameRepository->findOneBySlug($slug);
    }

    public function getOutstandingInvitationsForGame(Game $game): iterable
    {
        return $this->gameInvitationRepository->getOutstandingInvitationsForGame($game);
    }

    public function findInvitationByCode(string $invitationCode): ? GameInvitation {
        return $this->gameInvitationRepository->findByInvitationCode($invitationCode);
    }

    public function findInvitationByCodeAndEmailAddress(string $invitationCode, string $emailAddress): ? GameInvitation {
        return $this->gameInvitationRepository->findByInvitationCodeAndEmailAddress($invitationCode, $emailAddress);
    }

    public function getExpiredInvitations(): array
    {
        return $this->gameInvitationRepository->getExpiredInvitations();
    }

    public function getOutstandingInvitationsForUser(User $user): array
    {
        return $this->gameInvitationRepository->getOutstandingInvitationsForUser($user);
    }
}
