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
class GameManagerVoter extends Voter
{

    public const string MANAGE_GAME_ACTION = 'manage_game';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::MANAGE_GAME_ACTION) {
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
            $gamesMaster = $subject->getGamesMaster();

            if ($user === $gamesMaster) {
                return true;
            }
        }
        return false;
    }
}
