<?php

namespace App\Services\Puzzle\Domain\Interfaces;

use App\Services\Game\Domain\Game;

interface PuzzleInstanceInterface
{
    public function getTemplateSlug(): string;

    public function getInstanceCode(): string;

    public function setInstanceCode(string $instanceCode): static;

    public function getGame(): Game;

    public function getName(): string;

    public function getDescription(): string;

    public function setGame(Game $game): void;


    public function getPublicationDate(): ?\DateTimeInterface;

    public function isPublished(): bool;

    public function setPublicationDate(\DateTimeInterface $publicationDate): void;
}
