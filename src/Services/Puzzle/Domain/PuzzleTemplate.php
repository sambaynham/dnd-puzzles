<?php

namespace App\Services\Puzzle\Domain;

use App\Entity\AbstractDomainEntity;
use App\Entity\User;
use App\Services\Puzzle\Infrastructure\PuzzleTemplateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuzzleTemplateRepository::class)]
class PuzzleTemplate extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,

        #[ORM\Column(length: 1024)]
        private string $description,

        #[ORM\ManyToMany(targetEntity: PuzzleCategory::class)]
        private array $puzzleCategories,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'puzzlesAuthored')]
        private User $author,

        #[ORM\Column(type: 'json')]
        private array $configuration = [],
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPuzzleCategories(): array
    {
        return $this->puzzleCategories;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
