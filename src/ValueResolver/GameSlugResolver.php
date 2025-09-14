<?php

namespace App\ValueResolver;


use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class GameSlugResolver implements ValueResolverInterface
{
    public function __construct(private readonly GameRepository $gameRepository)
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

        $game = $this->gameRepository->findOneBy(['slug' => $slug]);

        // create and return the value object
        return $game ? [$game] : [];
    }
}
