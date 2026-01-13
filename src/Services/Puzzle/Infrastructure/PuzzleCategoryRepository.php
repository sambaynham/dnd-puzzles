<?php

namespace App\Services\Puzzle\Infrastructure;

use App\Services\Core\Infrastructure\AbstractValueObjectRepository;
use App\Services\Puzzle\Domain\PuzzleCategory;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractValueObjectRepository<PuzzleCategory>
 */
class PuzzleCategoryRepository extends AbstractValueObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PuzzleCategory::class);
    }

}
