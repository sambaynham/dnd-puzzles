<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service\Interfaces;

use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use Doctrine\Common\Collections\ArrayCollection;

interface PuzzleServiceInterface
{
    /**
     * @return iterable<PuzzleTemplate>
     */
    public function getTemplates(): iterable;

    public function getTemplateBySlug(string $categorySlug): ?PuzzleTemplate;

    public function getAllCategories(): array;

    public function getTemplatesByCategory(PuzzleCategory $category): ArrayCollection;

    public function getCategoryBySlug(string $categorySlug): ? PuzzleCategory;
}
