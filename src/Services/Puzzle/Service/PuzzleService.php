<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service;

use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use App\Services\Puzzle\Service\Interfaces\PuzzleServiceInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use Doctrine\Common\Collections\ArrayCollection;

class PuzzleService implements PuzzleServiceInterface
{
    public function __construct(
        private PuzzleTemplateRegistryInterface $templateRegistry,
        private PuzzleCategoryRepository $categoryRepository
    ) {}


    /**
     * @return iterable<PuzzleTemplate>
     */
    public function getTemplates(): iterable
    {
        return $this->templateRegistry->getTemplates();
    }

    public function getTemplateBySlug(string $categorySlug): ?PuzzleTemplate
    {
        return $this->templateRegistry->getTemplate($categorySlug);
    }

    /**
     * @return array<PuzzleCategory>
     */
    public function getAllCategories(): array {
        return $this->categoryRepository->findAll();
    }

    public function getTemplatesByCategory(PuzzleCategory $category): ArrayCollection
    {

        $collection = new ArrayCollection();
        foreach ($this->getTemplates() as $template) {
            foreach ($template->getCategories() as $puzzleCategory) {

                if ($puzzleCategory->getSlug() === $category->getSlug()) {

                    if (!$collection->contains($template)) {
                        $collection->add($template);
                    }
                }
            }
        }
        return $collection;
    }

    public function getCategoryBySlug(string $categorySlug): ?PuzzleCategory
    {
        return $this->categoryRepository->findOneBySlug($categorySlug);
    }
}
