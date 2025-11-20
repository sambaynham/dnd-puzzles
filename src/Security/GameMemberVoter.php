<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Game\Domain\Game;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GameMemberVoter extends Voter
{
    public const string PLAY_GAME_ACTION = 'play_game';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::PLAY_GAME_ACTION) {
            return false;
        }

        if (!$subject instanceof Game) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Game) {
            return false;
        }

        // Game master can always access
        if ($user === $subject->getGamesMaster()) {
            return true;
        }

        // Check if user is a player in the game
        return $subject->getPlayers()->contains($user);
    }
}
