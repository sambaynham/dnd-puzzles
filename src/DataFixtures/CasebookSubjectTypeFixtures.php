<?php

namespace App\DataFixtures;

use App\Services\Puzzle\Domain\Casebook\CasebookSubjectType;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectTypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CasebookSubjectTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        /**
         * @var CasebookSubjectTypeRepository $repo
         */
        $repo = $manager->getRepository(CasebookSubjectType::class);
        $types = [
            'person' => 'Person',
            'object' => 'Object',
            'location' => 'Location',
            'misc' => 'Misc'
        ];
        foreach ($types as $handle => $label) {
            if (!$repo->findOneByHandle($handle)) {
                $manager->persist(new CasebookSubjectType(label: $label, handle: $handle));
            }

        }

        $manager->flush();
    }
}
