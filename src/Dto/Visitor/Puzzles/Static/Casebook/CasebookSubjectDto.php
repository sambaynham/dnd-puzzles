<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

use App\Services\Puzzle\Domain\Casebook\Casebook;

class CasebookSubjectDto
{
    public function __construct(
        public readonly Casebook $casebook,
        public array $clues = [

        ],
        public ? string $name = null,
        public ? string $description = null
    ) {}
}
