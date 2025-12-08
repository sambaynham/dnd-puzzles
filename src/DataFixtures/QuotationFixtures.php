<?php

namespace App\DataFixtures;

use App\Services\Quotation\Domain\Quotation;
use App\Services\Quotation\Infrastructure\QuotationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class QuotationFixtures extends Fixture
{

    private const array QUOTATIONS = [
        1 => [
            'text' => 'Fight fire with Fireball',
            'citation' => 'Evocation Magic, a Primer'
        ],
        2 => [
            'text' => 'I\'d quite like to rage now, if that\'s alright with you.',
            'citation' => 'Bloodfang the unfailingly polite'
        ],
        3 => [
            'text' => 'Hot mindflayers in your area are waiting to eat you!',
            'citation' => 'Jourval the Obvious'
        ],
        4 => [
            'text' => 'We\'re three miles underground, Ugthruk. It was not \'just the wind\'. Check again.',
            'citation' => 'Dregthorth Rogues-bane'
        ],
        5 => [
            'text' => 'The more vowels you have in your name, the more Elvish you are. Random punctuation never hurt either.',
            'citation' => 'Anduri\'eauől the Unpronounceable'
        ],
        6 => [
            'text' => 'A Doppelgänger? Me? Sir, I refute the very suggestion. The notion is preposterous, not to mention slanderous. Why I\'ve never been so insulted in all my born days! You, my good fellow, shall be hearing from my solicitor. A doppelgänger, forsooth!',
            'citation' => '\'Grugthurk the Taciturn`\''
        ],

    ];

    public function load(ObjectManager $manager): void
    {
        /**
         * @var QuotationRepository $repository
         */
        $repository = $manager->getRepository(Quotation::class);
        foreach (self::QUOTATIONS as $id => $quotation) {
            if (null === $repository->find($id)) {
                $manager->persist(new Quotation(
                    quotation: $quotation['text'],
                    citation: $quotation['citation']
                ));
            }
        }
        $manager->flush();


    }


}
