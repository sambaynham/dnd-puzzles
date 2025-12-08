<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service;

use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;

class PuzzleTemplateRegistry implements PuzzleTemplateRegistryInterface
{
    /**
     * @param array<PuzzleTemplate> $templates
     */
    public function __construct(private array $templates) {

    }

    public function getTemplates(): array
    {
        return $this->templates;
    }

    public function getTemplate(string $slug): ?PuzzleTemplate
    {
        return $this->templates[$slug] ?? null;
    }

    public function getStaticTemplates(): array
    {
        return array_filter($this->getTemplates(), function (PuzzleTemplate $template) {
            return $template->isStatic();
        });
    }
}
