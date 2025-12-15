<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Infrastructure\Casebook\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;


class CasebookSubjectProvider implements ProviderInterface
{

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        die("HELLOWORLD");
    }
}
