<?php

namespace App\ValueResolver;


use App\Entity\Game;
use App\Entity\GameInvitation;
use App\Entity\Role;
use App\Repository\GameRepository;
use App\Repository\RoleRepository;
use App\Services\Puzzle\Infrastructure\GameInvitationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class RoleSlugResolver implements ValueResolverInterface
{
    public function __construct(private RoleRepository $roleRepository)
    {
    }
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();
        if (!$argumentType === Role::class) {
            return [];
        }


        $handle = $request->attributes->get('roleHandle');

        if (!is_string($handle)) {
            return [];
        }

        $role = $this->roleRepository->findOneByHandle($handle);
        // create and return the value object
        return $role ? [$role] : [];
    }
}
