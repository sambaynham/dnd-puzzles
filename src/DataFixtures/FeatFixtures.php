<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use App\Services\User\Domain\ValueObjects\Rarity;
use App\Services\User\Infrastructure\Repository\UserFeatRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FeatFixtures extends Fixture
{
    private const array FEATS = [
        'alpha_tester' => [
            'label' => 'Alpha Tester',
            'rarity' => 'l',
            'icon-class' => 'ra-aura',
            'description' => 'Took Part in the Alpha test',
            'games_master_awardable' => false
        ],
        'beta_tester' => [
            'label' => 'Beta Tester',
            'rarity' => 'e',
            'icon-class' => 'ra-aware',
            'description' => 'Took Part in the Beta test',
            'games_master_awardable' => false
        ],
        'sneaky' => [
            'label' => 'Sneaky',
            'rarity' => 'c',
            'icon-class' => 'ra-fedora',
            'description' => 'Thought of a devious way to solve a problem.',
            'games_master_awardable' => true
        ],
        'that_was_supposed_to_take_longer' => [
            'label' => 'That was Supposed to Take Longer',
            'rarity' => 'c',
            'icon-class' => 'ra-stopwatch',
            'description' => 'Beat a puzzle far too quickly.',
            'games_master_awardable' => true
        ],
        'you_can_certainly_try' => [
            'label' => 'You Can Certainly Try',
            'rarity' => 'c',
            'icon-class' => 'ra-uncertainty',
            'description' => 'Attempted something foolhardy/heroic',
            'games_master_awardable' => true
        ],
        'favoured_of_the_gods' => [
            'label' => 'Favoured of the Gods',
            'rarity' => 'u',
            'icon-class' => 'ra-aura',
            'description' => 'Beat a puzzle through sheer luck.',
            'games_master_awardable' => true
        ],
        'hint_hoarder' => [
            'label' => 'Hint Hoarder',
            'rarity' => 'c',
            'icon-class' => 'ra-quill-ink',
            'description' => 'Asked for hints',
            'games_master_awardable' => true
        ],
        'moderator' => [
            'label' => 'Moderator',
            'rarity' => 'l',
            'icon-class' => 'ra-heart-tower',
            'description' => 'Is a Community Moderator',
            'games_master_awardable' => false
        ],
        'administrator' => [
            'label' => 'Administrator',
            'rarity' => 'l',
            'icon-class' => 'ra-omega',
            'description' => 'Is a site Administrator',
            'games_master_awardable' => false,
        ],
        'contributor' => [
            'label' => 'Contributor',
            'rarity' => 'l',
            'icon-class' => 'ra-queen-crown',
            'description' => 'Has Contributed Puzzles or Art',
            'games_master_awardable' => false,

        ]
    ];

    /**
     * @throws InvalidHandleException
     * @throws UnmappedRarityException
     */
    public function load(ObjectManager $manager): void
    {
        /**
         * @var UserFeatRepository $featRepo
         */
        $featRepo = $manager->getRepository(UserFeat::class);
        foreach (self::FEATS as $handle => $definition) {
            if (!$featRepo->findByHandle($handle)) {
                $manager->persist(new UserFeat(
                    label: $definition['label'],
                    handle: $handle,
                    description: $definition['description'],
                    iconClass: $definition['icon-class'],
                    rarity: Rarity::makeFromRarityKey($definition['rarity']),
                    gamesMasterAwardable: $definition['games_master_awardable']
                ));
            }
        }
        $manager->flush();

    }
}
