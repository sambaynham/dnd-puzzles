<?php

namespace App\ValueResolver;


use App\Entity\Game;
use App\Entity\GameInvitation;
use App\Repository\GameRepository;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class GameInvitationResolver implements ValueResolverInterface
{
    public function __construct(private GameInvitationRepository $gameInvitationRepository)
    {
    }
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();
        if (!$argumentType === GameInvitation::class) {
            return [];
        }


        $code = $request->attributes->get('invitationCode');

        if (!is_string($code)) {
            return [];
        }

        $invitation = $this->gameInvitationRepository->findByInvitationCode($code);
        // create and return the value object
        return $invitation ? [$invitation] : [];
    }
}
