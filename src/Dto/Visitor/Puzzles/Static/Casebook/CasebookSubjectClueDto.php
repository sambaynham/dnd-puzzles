<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles\Static\Casebook;

use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Domain\Casebook\CasebookSubject;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClue;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClueType;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectType;
use Doctrine\Common\Collections\ArrayCollection;

class CasebookSubjectClueDto
{
    public function __construct(
        public ? string $title = null,
        public ? string $body = null,
        public ? CasebookSubjectClueType $type = null,
        public ? int $id = null
    ) {
    }

    public static function makeFromCasebookSubjectClue(CasebookSubjectClue $casebookSubjectClue): static {
        return new static(
            title: $casebookSubjectClue->getTitle(),
            body: $casebookSubjectClue->getBody(),
            type: $casebookSubjectClue->getType(),
            id: $casebookSubjectClue->getId()
        );
    }

    public function isBlank(): bool {
        return ($this->title === null && $this->body === null);
    }
}
