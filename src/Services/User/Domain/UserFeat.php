<?php

declare(strict_types=1);

namespace App\Services\User\Domain;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\User\Domain\ValueObjects\Rarity;
use App\Services\User\Infrastructure\Repository\UserFeatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserFeatRepository::class, readOnly: false)]
#[UniqueEntity(fields: ['handle'], message: 'There is already a feat with this handle. Please choose another one.')]
class UserFeat extends AbstractValueObject
{
    public function __construct(

        string $label,

        string $handle,

        #[ORM\Column(length: 512, nullable: false)]
        private string $description,

        #[ORM\Column(length: 128, nullable: false)]
        private string $iconClass,

        #[ORM\Column(type: 'rarity', nullable: false)]
        private Rarity $rarity,

        #[ORM\Column(type: 'boolean')]
        private bool $gamesMasterAwardable = false,

        ?int $id = null,

    ) {
        parent::__construct(
            label: $label,
            handle: $handle,
            id: $id
        );
    }

    public function getRarity(): Rarity
    {
        return $this->rarity;
    }

    public function setRarity(Rarity $rarity): void
    {
        $this->rarity = $rarity;
    }

    public function getIconClass(): string
    {
        return $this->iconClass;
    }

    public function setIconClass(string $iconClass): void
    {
        $this->iconClass = $iconClass;
    }


    public function getDescription(): string
    {
        return $this->description;
    }

    public function isGamesMasterAwardable(): bool
    {
        return $this->gamesMasterAwardable;
    }

    public function setGamesMasterAwardable(bool $gamesMasterAwardable): void
    {
        $this->gamesMasterAwardable = $gamesMasterAwardable;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public static function hasDescription(): bool
    {
        return true;
    }
}
