<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Infrastructure\Casebook\Repository;

use App\Services\Core\Infrastructure\AbstractValueObjectRepository;
use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClueType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractValueObjectRepository<CasebookSubjectClueType>
 */
class CasebookSubjectClueTypeRepository extends AbstractValueObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasebookSubjectClueType::class);
    }
}
