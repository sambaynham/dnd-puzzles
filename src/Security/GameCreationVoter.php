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
class GameCreationVoter extends Voter
{

    public const string CREATE_GAME_ACTION = 'create_game';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::CREATE_GAME_ACTION) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if ($user instanceof User) {
            return (count($user->getGamesMastered()) < $user->getUserAccountType()->getMaximumConcurrentGames());
        }
        return false;
    }
}
