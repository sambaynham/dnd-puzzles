<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\User\Domain\User;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
/**
 * @template-extends Voter<string, User>
 */
class PermissionVoter extends Voter
{
    public function __construct(private UserServiceInterface $userService) {}
    protected function supports(string $attribute, mixed $subject): bool
    {
        return null !== $this->userService->getPermissionByHandle($attribute);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if ($user instanceof User) {
            foreach ($user->getHydratedRoles() as $role) {
                if ($role->hasPermission($attribute)) {
                    return true;
                }
            }
        }
        return false;

    }
}
