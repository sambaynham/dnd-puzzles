<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Infrastructure\Casebook;

use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClueType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CasebookSubjectClueType>
 */
class CasebookSubjectClueTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasebookSubjectClueType::class);
    }
}
