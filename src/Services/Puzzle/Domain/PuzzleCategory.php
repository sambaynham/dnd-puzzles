<?php

namespace App\Services\Puzzle\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Core\Domain\AbstractValueObject;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PuzzleCategoryRepository::class)]

class PuzzleCategory extends AbstractValueObject
{
    public function __construct(
        string $label,
        string $handle,
        #[ORM\Column(length: 255)]
        private string $description,
        ?int $id = null,
    ) {
        parent::__construct($label, $handle, $id);
    }

    public function getDescription(): string
    {
        return $this->description;
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
