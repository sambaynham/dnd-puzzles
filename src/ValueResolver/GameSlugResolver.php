<?php

namespace App\ValueResolver;


use App\Services\Game\Domain\Game;
use App\Services\Game\Infrastructure\GameRepository;
use App\Services\Game\Service\GameService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class GameSlugResolver implements ValueResolverInterface
{
    public function __construct(private GameService $gameService)
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
