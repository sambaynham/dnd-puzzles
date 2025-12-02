<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

use Symfony\Component\Validator\Constraints as Assert;

class CasebookCreateDto
{
    public function __construct(
        public readonly string $puzzleName,
        #[Assert\NotBlank()]
        #[Assert\Length(min: 10, max:1024)]
        public ? string $brief = null
    ) {}
}
