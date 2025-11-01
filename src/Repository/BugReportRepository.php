<?php

namespace App\Repository;

use App\Entity\BugReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BugReport>
 */
class BugReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BugReport::class);
    }

    public function findUnactioned(): array {
        $qb  = $this->createQueryBuilder('br');
        return $qb
            ->where($qb->expr()->isNull('br.actionedAt'))
            ->andWhere($qb->expr()->isNull('br.closedAt'))
            ->getQuery()
            ->getResult();
    }
}
