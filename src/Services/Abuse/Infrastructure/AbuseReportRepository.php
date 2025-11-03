<?php

namespace App\Services\Abuse\Infrastructure;

use App\Services\Abuse\Domain\AbuseReport;
use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AbuseReport>
 */
class AbuseReportRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AbuseReport::class);
    }

    public function findUnactioned(): array {
        $qb = $this->createQueryBuilder('ar');
        $qb->where($qb->expr()->isNull('ar.checkedDate'));
        return $qb->getQuery()->getResult();
    }

    public function getAbuseReportsForReportedUser(User $user): array {
        $qb = $this->createQueryBuilder('ar');
        $qb
            ->where($qb->expr()->eq('ar.reportedUser', ':user'))
            ->andWhere($qb->expr()->isNotNull('ar.checkedDate'))
            ->andWhere($qb->expr()->isNotNull('ar.confirmedDate'));
        $qb->setParameter('user', $user);
        return $qb->getQuery()->getResult();
    }
}
