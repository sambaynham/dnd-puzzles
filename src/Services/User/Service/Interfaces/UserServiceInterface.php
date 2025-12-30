<?php

namespace App\Services\User\Service\Interfaces;

use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface UserServiceInterface
{
    public function saveUser(User $user): User;

    public function getRoleByHandle(string $roleHandle): ? Role;

    public function getPermissionByHandle(string $permissionHandle): ? Permission;

    public function redeemInvitationForUser(GameInvitation $invitation, User $user): void;

    public function validateUser(User $user): ConstraintViolationListInterface;

    public function getUsersPaginated(int $firstResult, int $maxResults = 50): iterable;

    public function findByEmailOrUserName(string $searchTerms, int $firstResult, int $maxResults = 50): iterable;

    public function getUsersCount(): int;

    public function findAllRoles(): array;

    public function findAllFeats(): array;
}
