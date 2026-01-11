<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Domain\Interfaces;

use App\Services\Game\Domain\Game;
use Doctrine\Common\Collections\Collection;

interface StaticPuzzleInstanceProviderInterface
{
    public function providesTemplateInstances(): string;

    /**
     * @param Game $game
     * @return Collection<int, PuzzleInstanceInterface>
     */
    public function getStaticPuzzleInstancesForGame(Game $game): Collection;

    public function getInstance(string $instanceCode): ? PuzzleInstanceInterface;
}
