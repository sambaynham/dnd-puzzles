<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service;

use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Domain\PuzzleTemplate;
use App\Services\Puzzle\Infrastructure\Casebook\CasebookRepository;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateServiceInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleTemplateRegistryInterface;
use Doctrine\Common\Collections\ArrayCollection;

class PuzzleTemplateService implements PuzzleTemplateServiceInterface
{
    private const string ALLOWABLE_SLUG_CHARACTER_PATTERN  = '/[^a-z_]/';
    public function __construct(
        private readonly PuzzleTemplateRegistryInterface $templateRegistry,
        private readonly PuzzleCategoryRepository $categoryRepository,
        private readonly CasebookRepository $casebookRepository
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

    public function generatePuzzleSlug(string $puzzleName, string $puzzleClass): string
    {
        $slug = strtolower($puzzleName);
        if (str_starts_with($slug, 'the ')) {
            $slug = substr($slug, 4);
        }
        $slug = str_replace(" ", "_", $slug);
        $slug = preg_replace(self::ALLOWABLE_SLUG_CHARACTER_PATTERN, '', $slug);

        if ($puzzleClass === Casebook::class) {
            return $this->casebookRepository->decollideSlug($slug);
        }
        return $slug;
    }


}
