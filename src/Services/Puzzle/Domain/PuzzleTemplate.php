<?php

namespace App\Services\Puzzle\Domain;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

readonly class PuzzleTemplate
{
    /**
     * @param string $slug
     * @param string $title
     * @param string $description
     * @param string $category
     * @param string $authorEmail
     * @param array<PuzzleCredit $credits
     * @param array<ConfigOptionDefinition> $configuration
     */
    public function __construct(
        private string $slug,
        private string $title,
        private \DateTimeImmutable $createdAt,
        private string $description,
        private string $category,
        private string $authorEmail,
        private array $credits = [],
        private array  $configuration = [],
    ) {
    }

    public function getCategory(): string
    {
        return $this->category;
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

}
