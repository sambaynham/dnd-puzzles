<?php

namespace App\Services\User\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\Exceptions\InvalidRoleHandleException;
use App\Services\User\Infrastructure\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Services\Core\Domain\AbstractValueObject;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[UniqueEntity(fields: ['handle'], message: 'There is already a permission with this handle. Please choose another one.')]
class Role extends AbstractValueObject
{
    private const string ROLE_HANDLE_PREFIX = 'ROLE_';

    /**
     * @throws InvalidRoleHandleException|\App\Services\Core\Domain\Exceptions\InvalidHandleException
     */
    public function __construct(
        string $label,
        string $handle,
        #[ORM\ManyToMany(targetEntity: Permission::class, fetch: 'EAGER', indexBy: 'handle')]
        private Collection $permissions = new ArrayCollection(),

        #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles', indexBy: 'handle')]
        private Collection $users = new ArrayCollection(),

        ?int $id = null
    )
    {
        if (substr($handle, 0, 5) !== self::ROLE_HANDLE_PREFIX) {
            throw new InvalidRoleHandleException(sprintf(
                'Role handles must begin with %s. %s is not valid',
                self::ROLE_HANDLE_PREFIX, $handle
            ));
        }

        parent::__construct(
            label: $label,
            handle: $handle,
            id: $id
        );
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): void
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
        }
    }

    public function removePermission(Permission $permission): void
    {
        $this->permissions->removeElement($permission);
    }

    public function hasPermission(string $permissionHandle): bool {
        foreach ($this->permissions as $permission) {
            if ($permission->getHandle() === $permissionHandle) {
                return true;
            }
        }
        return false;
    }

    public static function hasDescription(): bool
    {
        return false;
    }
}
