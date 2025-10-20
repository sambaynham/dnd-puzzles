<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain;

use App\Entity\AbstractDomainEntity;
use App\Services\Puzzle\Infrastructure\CasebookRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CasebookRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a casebook with this slug')]
class Casebook extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 255, unique: true)]
        private string $slug,

        #[ORM\Column(length: 65535)]
        private string $brief,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getBrief(): string
    {
        return $this->brief;
    }

    public function setBrief(string $brief): void
    {
        $this->brief = $brief;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
