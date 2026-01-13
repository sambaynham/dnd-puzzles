<?php

declare(strict_types=1);

namespace App\Doctrine\Types;

use App\Doctrine\Types\Exceptions\InvalidEmailException;
use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use App\Services\User\Domain\ValueObjects\Rarity;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EmailType extends Type
{
    public const string NAME = 'email';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getStringTypeDeclarationSQL($column);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param non-empty-string $value
     * @return non-empty-string
     * @throws InvalidEmailException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): string {
        $this->validateValue($value);
        return $value;
    }

    /**
     * @param non-empty-string $value
     * @return non-empty-string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        $this->validateValue($value);
        return $value;
    }

    /**
     * @throws InvalidEmailException
     */
    private function validateValue(mixed $value): void {
        if (!is_string($value) || strlen($value) === 0 || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidEmailException($value);
        }
    }
}
