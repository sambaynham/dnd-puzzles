<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;


class UserOwnsInvitationVoter extends Voter
{

    public const string REDEEM_INVITATION = 'redeem_invitation';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::REDEEM_INVITATION) {
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
        if ($user instanceof User && $subject instanceof GameInvitation) {
            $invitationEmail = $subject->getEmail();

            if ($user->getUserIdentifier() === $invitationEmail) {
                return true;
            }
        }
        return false;
    }
}
