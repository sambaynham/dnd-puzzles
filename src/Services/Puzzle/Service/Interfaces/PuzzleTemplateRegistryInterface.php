<?php

namespace App\Services\Puzzle\Service\Interfaces;

use App\Services\Puzzle\Domain\PuzzleTemplate;

interface PuzzleTemplateRegistryInterface
{
    /**
     * @return array<PuzzleTemplate>
     */
    public function getTemplates(): array;

    public function getTemplate(string $slug): ?PuzzleTemplate;

    /**
     * @return PuzzleTemplate[]
     */
    public function getStaticTemplates(): array;
}
