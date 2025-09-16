<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service\Interfaces;

use App\Services\Puzzle\Domain\PuzzleCategory;

interface PuzzleServiceInterface
{
    /**
     * @return iterable<PuzzleCategory>
     */
    public function getCategories(): iterable;

    public function getCategoryBySlug(string $categorySlug): ?PuzzleCategory;
}
