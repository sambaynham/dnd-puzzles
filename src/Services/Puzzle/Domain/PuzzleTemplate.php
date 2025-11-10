<?php

namespace App\Services\Puzzle\Domain;

use Doctrine\Common\Collections\ArrayCollection;

readonly class PuzzleTemplate
{
    /**
     * @param string $slug
     * @param string $title
     * @param string $description
     * @param ArrayCollection<PuzzleCategory> $categories
     * @param string $authorEmail
     * @param array<PuzzleCredit $credits
     * @param array<ConfigOptionDefinition> $configuration
     */
    public function __construct(
        private string $slug,
        private string $title,
        private \DateTimeImmutable $createdAt,
        private string $description,
        private ArrayCollection $categories,
        private string $authorEmail,
        private bool $static,
        private array $credits = [],
        private array  $configuration = [],
    ) {
    }

    public function getCategories(): ArrayCollection
    {
        return $this->categories;
    }

    public function getCredits(): array
    {
        return $this->credits;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }


    public function getAuthorEmail(): string
    {
        return $this->authorEmail;
    }


    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPuzzleCategories(): string
    {
        return $this->category;
    }
    public function getTitle(): string
    {
        return $this->title;
    }


    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isStatic(): bool {
        return $this->static;
    }
}
