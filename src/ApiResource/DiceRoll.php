<?php

declare(strict_types=1);

namespace App\ApiResource;


use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\Metadata\Link;
use App\State\DiceStateProvider;

#[ApiResource(
    uriTemplate: '/dice/{rollState}',
    uriVariables: [
        'rollState' => new Link(
            fromClass: DiceRoll::class,
            fromProperty: 'id'
        )
    ],
    operations: [
        new GetCollection()
    ],
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
