<?php

namespace App\Services\Puzzle\Domain;

use App\Entity\AbstractDomainEntity;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PuzzleCategoryRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a puzzle category with this slug')]
class PuzzleCategory extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 255, unique: true)]
        private readonly string $slug,

        #[ORM\Column(length: 255, unique: true)]
        private readonly string $label,

        ?int                    $id = null
    ) {
        parent::__construct($id);
    }


    public function getLabel(): string
    {
        return $this->label;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
