<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service;

use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use App\Services\Puzzle\Service\Interfaces\PuzzleServiceInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;

class PuzzleService implements PuzzleServiceInterface
{
    public function __construct(
        private PuzzleTemplateRegistryInterface $templateRegistry
    ) {}


    public function getTemplates(): iterable
    {
        return $this->templateRegistry->getTemplates();
    }

    public function getTemplateBySlug(string $categorySlug): ?PuzzleTemplate
    {
        return $this->templateRegistry->getTemplate($categorySlug);
    }
}
