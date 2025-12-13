<?php

namespace App\Services\User\Domain;

use App\Services\User\Infrastructure\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PermissionRepository::class, readOnly: true)]
#[UniqueEntity(fields: ['handle'], message: 'There is already a permission with this handle. Please choose another one.')]
class Permission
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private readonly string $label,

        #[ORM\Column(length: 255, unique:true)]
        private readonly string $handle,

        #[ORM\Column(length: 255)]
        private readonly string $description,

        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null
    ){
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

}

