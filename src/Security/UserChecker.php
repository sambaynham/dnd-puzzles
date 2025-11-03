<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\User\Domain\User as AppUser;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    private const string PERMABLOCK_MESSAGE_PATTERN = 'Your user has been permanently blocked because %s.';

    private const string TEMPORARY_BLOCK_MESSAGE_PATTERN = 'Your user has been temporarily blocked because %s. The block will expire on %s';
    public function checkPreAuth(UserInterface $user): void
    {

        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->isBlocked()) {
            $block = $user->getUserBlock();

            $message = $block->isPermanent()
                ? sprintf(self::PERMABLOCK_MESSAGE_PATTERN, $block->getReason())
                : sprintf(self::TEMPORARY_BLOCK_MESSAGE_PATTERN, $block->getReason(), $block->getExpirationDate()?->format('Y-m-d H:i'));
            throw new CustomUserMessageAccountStatusException(message: $message);
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // TODO: Implement checkPostAuth() method.
    }
}
