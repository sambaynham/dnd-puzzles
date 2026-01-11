<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\User\Domain\ValueObjects;

use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use App\Services\User\Domain\ValueObjects\Rarity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RarityTest extends TestCase
{
    public static function provideRarityKeys(): iterable {
        yield 'common' => [
            'key' => 'c',
            'expectedLabel' => 'Common',
            'expectedClassSafeLabel' => 'common'
        ];

        yield 'uncommon' => [
            'key' => 'u',
            'expectedLabel' => 'Uncommon',
            'expectedClassSafeLabel' => 'uncommon'
        ];

        yield 'rare' => [
            'key' => 'r',
            'expectedLabel' => 'Rare',
            'expectedClassSafeLabel' => 'rare'
        ];

        yield 'epic' => [
            'key' => 'e',
            'expectedLabel' => 'Epic',
            'expectedClassSafeLabel' => 'epic'
        ];

        yield 'legendary' => [
            'key' => 'l',
            'expectedLabel' => 'Legendary',
            'expectedClassSafeLabel' => 'legendary'
        ];
    }

    /**
     * @throws UnmappedRarityException
     */
    #[DataProvider('provideRarityKeys')]
    public function testMakeFromRarityKey(string $key, string $expectedLabel, string $expectedClassSafeLabel): void {
        $rarity = Rarity::makeFromRarityKey($key);
        self::assertEquals($expectedLabel, $rarity->getLabel());
        self::assertEquals($key, $rarity->getKey());
        self::assertEquals($expectedClassSafeLabel, $rarity->getClassSafeLabel());
        self::assertEquals($key, (string) $rarity);
    }
}
