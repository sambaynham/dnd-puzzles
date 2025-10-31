<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
#[UniqueEntity(fields: ['handle'], message: 'There is already a permission with this handle. Please choose another one.')]
class Permission extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $label,

        #[ORM\Column(length: 255, unique:true)]
        private readonly string $handle,

        #[ORM\Column(length: 255)]
        private string $description,

        /**
         * @var Collection<int, Role>
         */
        #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'permissions')]
        private Collection $roles = new ArrayCollection(),

        ?int $id = null
    ){

        parent::__construct($id);
    }


    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->addPermission($this);
        }
    }

    public function removeRole(Role $role): void
    {
        if ($this->roles->removeElement($role)) {
            $role->removePermission($this);
        }
    }
}

