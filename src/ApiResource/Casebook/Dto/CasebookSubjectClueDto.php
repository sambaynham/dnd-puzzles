<?php

declare(strict_types=1);

namespace App\ApiResource\Casebook\Dto;

use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClue;

class CasebookSubjectClueDto
{
    public function __construct(
        public $id,
        public string $title,
        public string $body,
        public string $type,
        public \DateTimeInterface $updatedAt,
        public ? \DateTimeInterface $revealedDate = null
    ) {}

    public static function makeFromCasebookSubjectClue(CasebookSubjectClue $clue): static {
        return new static(
            id: $clue->getId(),
            title: $clue->getTitle(),
            body: $clue->getBody(),
            type: $clue->getType()->getHandle(),
            updatedAt: $clue->getUpdatedAt(),
            revealedDate: $clue->getRevealedDate()
        );
    }
}
