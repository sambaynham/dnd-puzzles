<?php

declare(strict_types=1);

namespace App\ApiResource\Casebook\Dto;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use App\ApiResource\Casebook\Providers\CasebookProvider;
use App\ApiResource\Casebook\Providers\CasebookSubjectProvider;
use App\Services\Puzzle\Domain\Casebook\CasebookSubject;

#[ApiResource(
    uriTemplate: '/puzzles/static/casebook/{instanceCode}/subjects/{subjectId}',
    operations: [
        new Get()
    ],
    uriVariables: [
        'instanceCode' => new Link(
            fromProperty: 'instanceCode',
            fromClass: CasebookDto::class
        ),

        'subjectId' => new Link(
            fromProperty: 'id',
            fromClass: CasebookSubjectDto::class
        ),
    ],
    stateless: false,
    provider: CasebookSubjectProvider::class
)]
class CasebookSubjectDto
{

    /** @var \App\Dto\Visitor\Puzzles\Static\Casebook\CasebookSubjectClueDto[] */
    #[Link(toProperty: 'subject')]
    public $clues = [];

    public function __construct(
        public int $id,
        public string $name,
        public string $description,
        public string $type,
        public ? string $imageUri = null
    ) {
    }

    public static function makeFromSubject(CasebookSubject $subject): static {

        return new static (
            id: $subject->getId(),
            name: $subject->getName(),
            description: $subject->getDescription(),
            type: $subject->getCasebookSubjectType()->getHandle(),
            imageUri: $subject->getCasebookSubjectImage(),
        );
    }
}
