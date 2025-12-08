<?php

namespace App\Services\Puzzle\Service\Interfaces;

use App\Services\Puzzle\Domain\PuzzleTemplate;

interface PuzzleTemplateRegistryInterface
{
    /**
     * @return array<PuzzleTemplate>
     */
    public function getTemplates(): array;

    public function getStaticTemplates(): array;
}
