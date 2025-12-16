<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Puzzles;

class PublishPuzzleInstanceDto
{
    public function __construct(
        public \DateTimeInterface $publicationDate = new \DateTime()
    ) {}
}
