<?php

namespace App\Services\Puzzle\Domain\Interfaces;

use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\PuzzleTemplate;

interface PuzzleInstanceInterface
{
    public function getTemplateSlug(): string;

    public function getInstanceCode(): string;

    public function setInstanceCode(string $instanceCode): void;

    public function getGame(): Game;

    public function getName(): string;

    public function getDescription(): string;

    public function setGame(Game $game): void;


    public function getPublicationDate(): ?\DateTimeInterface;

    public function isPublished(): bool;

    public function setPublicationDate(\DateTimeInterface $publicationDate): void;

    public function getTemplate(): PuzzleTemplate;

    public function setTemplate(PuzzleTemplate $puzzleTemplate): void;
}
