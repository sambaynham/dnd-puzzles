<?php

declare(strict_types=1);

namespace App\Services\User\Service;

use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Infrastructure\PermissionRepository;
use App\Services\User\Infrastructure\RoleRepository;
use App\Services\User\Infrastructure\UserBlockRepository;
use App\Services\User\Infrastructure\UserRepository;
use App\Services\User\Service\Exceptions\MissingDefaultRoleException;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class UserService implements UserProviderInterface, UserServiceInterface
{
    private const string DEFAULT_ROLE = 'ROLE_USER';
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PermissionRepository $permissionRepository,
        private readonly RoleRepository $roleRepository,
        private readonly UserBlockRepository $blockRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }
        return $this->userRepository->find($user->getId());
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $identifier]);
        if (!$user) {
            throw new UserNotFoundException();
        }
        return $user;
    }

    public function saveUser(User $user): User
    {
        return null === $user->getId() ? $this->createUser($user) : $this->updateUser($user);
    }

    /**
     * @throws MissingDefaultRoleException
     */
    private function createUser(User $user): User {
        if (count($user->getRoles()) === 0) {
            $role = $this->getRoleByHandle(self::DEFAULT_ROLE);
            if ($role === null) {
                throw new MissingDefaultRoleException(sprintf("Default role '%s' is not present in the database.", self::DEFAULT_ROLE));
            }
            $user->setRoles(new ArrayCollection([$role]));

        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    private function updateUser(User $user): User {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function getRoleByHandle(string $roleHandle): ? Role {
        return $this->roleRepository->findOneBy(['handle' => $roleHandle]);
    }

    public function redeemInvitationForUser(GameInvitation $invitation, User $user): void
    {
        $invitation->setUser($user);
        $invitation->markUsed();
        $game = $invitation->getGame();
        $game->addPlayer($user);
        $this->entityManager->persist($invitation);
        $this->entityManager->persist($game);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function validateUser(User $user): ConstraintViolationListInterface
    {
        return $this->validator->validate($user);
    }

    public function getPermissionByHandle(string $permissionHandle): ?Permission
    {
        return $this->permissionRepository->findOneBy(['handle' => $permissionHandle]);
    }

    public function getUsersPaginated(int $firstResult, int $maxResults = 50): iterable
    {
        return $this->userRepository->findAllPaginated($firstResult, $maxResults);
    }

    public function findByEmailOrUserName(string $searchTerms, int $firstResult, int $maxResults = 50): iterable
    {
        return $this->userRepository->searchByEmailOrUserName($searchTerms, $firstResult, $maxResults);
    }

    public function getUsersCount(): int
    {
        return $this->userRepository->getUsersCount();
    }

    public function findAllRoles(): array
    {
        return $this->roleRepository->findAll();
    }
}
