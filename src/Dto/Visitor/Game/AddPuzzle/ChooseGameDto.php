<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Game\AddPuzzle;

use App\Services\Game\Domain\Game;
use Symfony\Component\Validator\Constraints as Assert;

class ChooseGameDto
{
    public function __construct(
        #[Assert\NotBlank]
        public string $template,
        public ? Game $game = null,
        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 255)]
        public ? string $puzzleName = null
    ) {}
}
