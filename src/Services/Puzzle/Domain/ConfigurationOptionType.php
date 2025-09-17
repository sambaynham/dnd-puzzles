<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain;

readonly class ConfigurationOptionType
{
    public function __construct(
        public string $typeName,
        public string $typeHandle
    ) {}
}
