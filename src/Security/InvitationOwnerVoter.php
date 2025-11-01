<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Game;
use App\Entity\GameInvitation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class InvitationOwnerVoter extends Voter
{

    public const string MANAGE_INVITATION_ACTION = 'manage_invitation';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::MANAGE_INVITATION_ACTION) {
            return false;
        }


        if (!$subject instanceof GameInvitation) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if ($subject instanceof GameInvitation) {

            $gamesMaster = $subject->getGame()->getGamesMaster();

            if ($user === $gamesMaster) {
                return true;
            }
        }
        return false;
    }
}
