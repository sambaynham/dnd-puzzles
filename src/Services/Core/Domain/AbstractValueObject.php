<?php

declare(strict_types=1);

namespace App\Services\Core\Domain;

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['handle'], message: 'There is already a value object of this type with this slug')]
abstract class AbstractValueObject extends AbstractDomainEntity implements \Stringable
{
    protected const string VALID_HANDLE_REGEX = '/\A[A-Za-z0-9_]+\z/';

    /**
     * @param string $label
     * @param string $handle
     * @param int|null $id
     * @throws InvalidHandleException
     */
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $label,

        #[ORM\Column(length: 255, unique: true)]
        private string $handle,

        ?int $id = null,
    ) {
        if (!preg_match(self::VALID_HANDLE_REGEX, $this->handle)) {
            throw new InvalidHandleException(
                'Handles may contain only letters, digits and underscores'
            );
        }

        parent::__construct($id);
    }

    final public function getLabel(): string
    {
        return $this->label;
    }

    final public function getHandle(): string
    {
        return $this->handle;
    }

    final public function __toString(): string
    {
        return $this->getLabel();
    }

    abstract public static function hasDescription(): bool;
}
