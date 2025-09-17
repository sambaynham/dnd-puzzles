<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain;

readonly class PuzzleCredit
{
    public function __construct(
        public string $name,
        public string $created,
        public ? string $email = null

    ) {

    }
}
