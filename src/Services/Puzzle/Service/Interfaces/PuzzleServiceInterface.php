<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service\Interfaces;

use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Domain\PuzzleTemplate;

interface PuzzleServiceInterface
{
    /**
     * @return iterable<PuzzleTemplate>
     */
    public function getTemplates(): iterable;

    public function getTemplateBySlug(string $categorySlug): ?PuzzleTemplate;

    public function getAllCategories(): array;
}
