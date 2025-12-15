<?php

namespace App\Services\User\Domain;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\User\Infrastructure\PermissionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PermissionRepository::class, readOnly: true)]
#[UniqueEntity(fields: ['handle'], message: 'There is already a permission with this handle. Please choose another one.')]
class Permission extends AbstractValueObject
{
    public function __construct(

        string $label,

        string $handle,

        #[ORM\Column(length: 255)]
        private readonly string $description,

        ?int $id = null
    ){
        parent::__construct(
            label: $label,
            handle: $handle,
            id: $id
        );
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public static function hasDescription(): bool
    {
        return true;
    }
}

