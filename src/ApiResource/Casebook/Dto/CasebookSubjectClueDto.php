<?php

declare(strict_types=1);

namespace App\ApiResource\Casebook\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use App\ApiResource\Casebook\Providers\CasebookSubjectClueProvider;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClue;

class CasebookSubjectClueDto
{
    public function __construct(
        public int $id,
        public string $title,
        public string $body,
        public string $type,
        public ? \DateTimeInterface $revealedDate = null,
    ) {}

    public static function makeFromCasebookSubjectClue(CasebookSubjectClue $clue): static {
        return new static(
            id: $clue->getId(),
            title: $clue->getTitle(),
            body: $clue->getBody(),
            type: $clue->getType()->getHandle(),
            revealedDate: $clue->getRevealedDate(),
        );
    }
}
