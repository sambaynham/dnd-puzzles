<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Game\Domain\Game;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, Game>
 */
class GamePlayerVoter extends Voter
{

    public const string PLAY_GAME = 'play_game';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::PLAY_GAME) {
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
        if ($user instanceof User && $subject instanceof Game) {
            if ($subject->getGamesMaster()->getUserIdentifier() === $user->getUserIdentifier()) {
                return true;
            }
            foreach ($subject->getPlayers() as $player) {
                if ($player->getUserIdentifier() === $user->getUserIdentifier()) {
                    return true;
                }
            }
        }
        return false;
    }
}
