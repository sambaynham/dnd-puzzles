<?php

declare(strict_types=1);

namespace App\Twig\Extensions;

use App\Services\Game\Domain\Game;
use App\Services\User\Domain\User;
use Twig\Attribute\AsTwigFunction;

class TwigGameExtensions
{
    #[AsTwigFunction('is_game_manager')]
    public function isGameManager(User $user, Game $game): bool {
        $gamesMaster = $game->getGamesMaster();

        return ($user === $gamesMaster);
    }
}
