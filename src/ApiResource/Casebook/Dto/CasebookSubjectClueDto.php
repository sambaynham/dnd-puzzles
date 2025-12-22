<?php

declare(strict_types=1);

namespace App\ApiResource\Casebook\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;

use App\ApiResource\Casebook\Providers\CasebookSubjectClueProvider;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClue;

#[ApiResource(
    uriTemplate: '/puzzles/static/casebook/{instanceCode}/subjects/{subjectId}/clues/{clueId}',
    operations: [ new Get() ],
    uriVariables: [
        'subjectId' => new Link(toProperty: 'subject', fromClass: CasebookSubjectDto::class),
        'clueId' => new Link(fromClass: CasebookSubjectClueDto::class),
    ]
)]
#[ApiResource(
    uriTemplate: '/puzzles/static/casebook/{instanceCode}/subjects/{subjectId}/clues',
    operations: [ new GetCollection() ],
    uriVariables: [
        'subjectId' => new Link(toProperty: 'subject', fromClass: CasebookSubjectDto::class),
    ]
)]
class CasebookSubjectClueDto
{
    public function __construct(
        public $id,
        public string $title,
        public string $body,
        public string $type,
        public \DateTimeInterface $updatedAt,
        public ? \DateTimeInterface $revealedDate = null,
        public ? CasebookSubjectDto $subject = null
    ) {}

    public static function makeFromCasebookSubjectClue(CasebookSubjectClue $clue): static {
        return new static(
            id: $clue->getId(),
            title: $clue->getTitle(),
            body: $clue->getBody(),
            type: $clue->getType()->getHandle(),
            updatedAt: $clue->getUpdatedAt(),
            revealedDate: $clue->getRevealedDate(),
            subject: CasebookSubjectDto::makeFromSubject($clue->getCasebookSubject())
        );
    }
}
