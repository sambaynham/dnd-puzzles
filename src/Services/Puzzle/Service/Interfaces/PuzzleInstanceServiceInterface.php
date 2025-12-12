<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service\Interfaces;

use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface PuzzleInstanceServiceInterface
{
    public function getInstanceByTemplateAndCode(string $templateSlug, string $instanceCode): ?PuzzleInstanceInterface;

    public function saveInstance(PuzzleInstanceInterface $instance): void;

    public function deleteInstance(PuzzleInstanceInterface $instance): void;

    public function getStaticPuzzleInstancesForGame(Game $game): ArrayCollection;
}
