<?php

declare(strict_types=1);

namespace App\Services\Core\Domain;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['handle'], message: 'There is already a value object of this type with this slug')]
abstract class AbstractValueObject extends AbstractDomainEntity implements \Stringable
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $label,

        #[ORM\Column(length: 255, unique: true)]
        private string $handle,

        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    final public function getLabel(): string
    {
        return $this->label;
    }

    final public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    final public function getHandle(): string
    {
        return $this->handle;
    }

    final public function setHandle(string $handle): void
    {
        $this->handle = $handle;
    }

    final public function __toString(): string
    {
        return $this->getLabel();
    }
}
