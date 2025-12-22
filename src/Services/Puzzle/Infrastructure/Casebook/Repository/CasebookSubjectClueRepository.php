<?php

namespace App\Services\Puzzle\Infrastructure\Casebook\Repository;

use App\Services\Puzzle\Domain\Casebook\CasebookSubjectClue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CasebookSubjectClue>
 */
class CasebookSubjectClueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CasebookSubjectClue::class);
    }

    public function getRevealedCluesBySubjectId(int $subjectId): array {
        $qb = $this->createQueryBuilder('csc');
        $qb->select('csc')
            ->innerJoin('csc.casebookSubject', 'csb', 'csb.id = csc.casebookSubjectId')
            ->where('csb.id = :subjectId')
            ->andWhere($qb->expr()->isNotNull('csc.revealedDate'))
            ->setParameter('subjectId', $subjectId)
            ->orderBy('csc.revealedDate', 'ASC');
        return $qb->getQuery()->getResult();

    }

}
