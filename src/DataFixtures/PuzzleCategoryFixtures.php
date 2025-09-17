<?php

namespace App\DataFixtures;

use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\Puzzle\Infrastructure\PuzzleCategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PuzzleCategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * @var PuzzleCategoryRepository $repo
         */
        $repo = $manager->getRepository(PuzzleCategory::class);
        $puzzleCategories = [
            'switch' => [
                'label' => 'Switch',
                'description' => 'In switch puzzles, a party must set a bank of switches in the correct configuration.',
            ],
            'button' => [
                'label' => 'Button',
                'description' => 'In button puzzles, a party must press buttons in a particular order, with immediate consequences for failure.'
            ],
            'logic' => [
                'label' => 'Logic',
                'description' => 'In a logic puzzle, players must use clues to make logical deductions about a fact. '
            ],
            'riddle' => [
                'label' => 'Riddles',
                'description' => 'In a riddle/wordplay puzzle, players must answer a question correctly'
            ]
        ];
        foreach ($puzzleCategories as $slug => $definition) {
            if (!$repo->findBySlug($slug)) {
                $manager->persist(new PuzzleCategory(slug: $slug, label: $definition['label'], description: $definition['description']));
            }

        }

        $manager->flush();
    }
}
