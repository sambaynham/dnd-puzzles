<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\State\DiceStateProvider;

#[ApiResource(
    uriTemplate: '/dice/{rollState}',
    operations: [
        new GetCollection()
    ],
    uriVariables: [
        'rollState' => new Link(
            fromProperty: 'id',
            fromClass: DiceRoll::class
        )
    ],
    stateless: false,
    provider: DiceStateProvider::class
)]
readonly class DiceRoll
{
    public function __construct(
        public string $id,
        public int $dieSides,
        public int $roll,
        public int $total,
        public ?int $bonus
    ) {

    }
}
