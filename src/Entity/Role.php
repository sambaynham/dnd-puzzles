<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[UniqueEntity(fields: ['handle'], message: 'There is already a permission with this handle. Please choose another one.')]
class Role extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 255, unique: true)]
        private readonly string $handle,

        #[ORM\ManyToMany(targetEntity: Permission::class, inversedBy: 'roles')]
        private Collection $permissions = new ArrayCollection(),

        #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'roles')]
        private Collection $users = new ArrayCollection(),

        ?int $id = null
    )
    {
        parent::__construct($id);
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getHandle(): string
    {
        return $this->handle;
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
}
