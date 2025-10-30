<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Game;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


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
        if ($subject instanceof Game) {
            $gamesMaster = $subject->getGamesMaster();
            if ($user === $gamesMaster) {
                return true;
            }
        }
        return false;
    }
}
