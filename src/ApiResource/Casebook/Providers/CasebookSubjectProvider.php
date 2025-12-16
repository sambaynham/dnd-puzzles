<?php

declare(strict_types=1);

namespace App\ApiResource\Casebook\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Casebook\Dto\CasebookDto;
use App\ApiResource\Casebook\Dto\CasebookSubjectDto;
use App\Services\Puzzle\Domain\Casebook\CasebookSubject;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookRepository;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;


class CasebookSubjectProvider implements ProviderInterface
{
    public function __construct(private CasebookSubjectRepository $casebookSubjectRepository) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$uriVariables['subjectId']) {
            throw new UnprocessableEntityHttpException("Subject Id not found.");
        }

        $subject = $this->casebookSubjectRepository->find($uriVariables['subjectId']);
        return CasebookSubjectDto::makeFromSubject($subject);

    }
}
