<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain\Interfaces;

interface StaticPuzzleInstanceInterface extends PuzzleInstanceInterface
{
    public function getTemplateSlug(): string;
}
