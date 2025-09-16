<?php

namespace App\DataFixtures;

use App\Services\Puzzle\Domain\PuzzleCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $puzzleCategories = [
            'switch' => 'Switch',
            'button' => 'Button',
            'logic' => 'Logic',
        ];
        foreach ($puzzleCategories as $slug => $label) {
            $manager->persist(new PuzzleCategory(slug: $slug, label: $label));
        }

        $manager->flush();
    }
}
