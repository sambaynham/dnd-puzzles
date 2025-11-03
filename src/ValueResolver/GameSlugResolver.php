<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Game\Domain\Game;
use App\Services\Game\Service\Interfaces\GameServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class GameSlugResolver implements ValueResolverInterface
{
    public function __construct(private GameServiceInterface $gameService)
    {
    }
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();
        if (!$argumentType === Game::class) {
            return [];
        }

        $slug = $request->attributes->get('slug');

        if (!is_string($slug)) {
            return [];
        }

        $game = $this->gameService->findOneBySlug($slug);

        // create and return the value object
        return $game ? [$game] : [];
    }
}
