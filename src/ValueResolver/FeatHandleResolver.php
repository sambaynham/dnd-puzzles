<?php

namespace App\ValueResolver;

use App\Services\User\Domain\UserFeat;
use App\Services\User\Infrastructure\Repository\UserFeatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class FeatHandleResolver implements ValueResolverInterface
{
    public function __construct(private UserFeatRepository $featRepository)
    {
    }
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. BookingId)
        $argumentType = $argument->getType();
        if ($argumentType !== UserFeat::class) {
            return [];
        }


        $handle = $request->attributes->get('featHandle');

        if (!is_string($handle)) {
            return [];
        }

        $role = $this->featRepository->findByHandle($handle);
        // create and return the value object
        return $role ? [$role] : [];
    }
}
