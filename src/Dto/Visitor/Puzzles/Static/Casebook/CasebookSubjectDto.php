<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Domain\Casebook\CasebookSubject;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CasebookSubjectDto
{
    public function __construct(
        public readonly Casebook $casebook,
        public ? CasebookSubjectType $type = null,
        public array $clues = [],
        public ? string $name = null,
        public ? string $description = null,
        public ? string $image = null,
    ) {}

    public static function makeFromCasebookSubject(CasebookSubject $casebookSubject): self {
        return new self(
            casebook: $casebookSubject->getCasebook(),
            type: $casebookSubject->getCasebookSubjectType(),
            clues: self::mapClues($casebookSubject->getCasebookSubjectClues()),
            name: $casebookSubject->getName(),
            description: $casebookSubject->getDescription(),
            image: $casebookSubject->getCasebookSubjectImage()
        );
    }

    private static function mapClues(Collection $clues): array {

        $arrayClues = [];
        foreach ($clues as $clue) {
            $arrayClues[] = CasebookSubjectClueDto::makeFromCasebookSubjectClue($clue);
        }
        return $arrayClues;

    }
}
