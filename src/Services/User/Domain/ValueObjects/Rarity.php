<?php

declare(strict_types=1);

namespace App\Services\User\Domain\ValueObjects;

use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use Stringable;

class Rarity implements Stringable
{
    private const array KNOWN_KEYS = [
        'c' => 'Common',
        'u' => 'Uncommon',
        'r' => 'Rare',
        'e' => 'Epic',
        'l' => 'Legendary'
    ];

    private function __construct(
        private readonly string $key,
        private readonly string $label,
    ) {}

    /**
     * @throws UnmappedRarityException
     */
    public static function makeFromRarityKey(string $key): self {
        if (!array_key_exists($key, self::KNOWN_KEYS)) {
            throw new UnmappedRarityException(
                sprintf("Unknown rarity key '%s'", $key)
            );
        }
        return new static(
            key: $key,
            label: self::KNOWN_KEYS[$key],
        );
    }

    public function getKey(): string {
        return $this->key;
    }

    public function getLabel(): string {
        return $this->label;
    }

    public function getClassSafeLabel(): string {
        return strtolower($this->label);
    }

    public function __toString(): string
    {
        return $this->key;
    }
}
