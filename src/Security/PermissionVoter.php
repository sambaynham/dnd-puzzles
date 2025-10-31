<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Repository\PermissionRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PermissionVoter extends Voter
{
    public function __construct(private PermissionRepository $permissionRepository) {}
    protected function supports(string $attribute, mixed $subject): bool
    {

        return null !== $this->permissionRepository->findOneByHandle($attribute);
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
