<?php

declare(strict_types=1);

namespace App\ApiResource\Casebook\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Casebook\Dto\CasebookDto;
use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class CasebookProvider implements ProviderInterface
{
    public function __construct(private CasebookRepository $casebookRepository) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$uriVariables['instanceCode']) {
            throw new UnprocessableEntityHttpException("Instance Code not set.");
        }

        $instance = $this->casebookRepository->getInstance($uriVariables['instanceCode']);
        if ($instance instanceof Casebook) {
            return CasebookDto::makeFromInstance($instance);
        }

    }
}
