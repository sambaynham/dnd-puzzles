<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use App\Services\User\Domain\ValueObjects\Rarity;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class RarityType extends Type
{
    public const string NAME = 'rarity';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @throws UnmappedRarityException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return Rarity::makeFromRarityKey($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!$value instanceof Rarity) {
            throw new UnmappedRarityException("Trying to map an instance of rarity that is not a rarity type object.");
        }
        return $value->getKey();
    }
}
