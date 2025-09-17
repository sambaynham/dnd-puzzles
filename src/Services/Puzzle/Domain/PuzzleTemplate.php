<?php

namespace App\Services\Puzzle\Domain;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

class PuzzleTemplate
{
    public function __construct(
        private string $slug,
        private string $title,
        private string $description,
        private array $puzzleCategories,
        private string $authorEmail,
        private array $configuration = [],
    ) {
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }

    public function setAuthorEmail(string $authorEmail): void
    {
        $this->authorEmail = $authorEmail;
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

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
