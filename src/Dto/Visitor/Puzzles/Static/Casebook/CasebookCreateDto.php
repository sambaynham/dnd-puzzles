<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

class CasebookCreateDto
{
    public function __construct(
        public readonly string $puzzleName,
        public ? string $brief = null
    ) {}
}
