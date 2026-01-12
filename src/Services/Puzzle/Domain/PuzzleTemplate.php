<?php

namespace App\Services\Puzzle\Domain;

use App\Services\Puzzle\Domain\Exceptions\NonStaticConfigurationAttemptException;
use App\Services\Puzzle\Domain\Exceptions\RoutelessStaticConfigurationAttemptException;
use Doctrine\Common\Collections\ArrayCollection;

readonly class PuzzleTemplate
{
    /**
     * @param string $slug
     * @param string $title
     * @param \DateTimeImmutable $createdAt
     * @param string $description
     * @param ArrayCollection<PuzzleCategory> $categories
     * @param string $authorEmail
     * @param bool $static
     * @param array<PuzzleCredit $credits
     * @param array<int, ConfigOptionDefinition> $configuration
     * @param string|null $staticCreateRoute
     * @param string|null $staticEditRoute
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
        private ? string $staticCreateRoute = null,
        private ? string $staticEditRoute = null,
        private ? string $staticPlayRoute = null,
    ) {
    }



    /**
     * @return ArrayCollection<PuzzleCategory>
     */
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


    /**
     * @return array<int, ConfigOptionDefinition>
     */
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

    public function getStaticCreateRoute(): ? string {
        $this->validateStatic();
        return $this->staticCreateRoute;
    }

    public function getStaticEditRoute(): ?string
    {
        $this->validateStatic();
        return $this->staticEditRoute;
    }

    public function getStaticPlayRoute(): ? string {
        $this->validateStatic();
        return $this->staticPlayRoute;
    }

    /**
     * @return void
     * @throws NonStaticConfigurationAttemptException
     * @throws RoutelessStaticConfigurationAttemptException
     */
    private function validateStatic(): void {
        if (!$this->isStatic()) {
            throw new NonStaticConfigurationAttemptException("This template is dynamic, and must be configured using the standard configuration route");
        } elseif ($this->staticCreateRoute === null || $this->staticEditRoute === null || $this->staticPlayRoute === null) {
            throw new RoutelessStaticConfigurationAttemptException("This template is static, but a static configuration route has not been provided.");
        }
    }
}
