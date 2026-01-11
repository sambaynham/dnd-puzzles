<?php

namespace App\Services\Quotation\Infrastructure;

use App\Services\Quotation\Domain\Quotation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Quotation>
 */
class QuotationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Quotation::class);
    }

    public function getRandomQuotationId(): int {
        $qb = $this->createQueryBuilder('q');
        $qb->select('q.id');
        $results = $qb->getQuery()->getSingleColumnResult();

        $mappedResults = [];
        foreach ($results as $result) {
            if (is_int($result)) {
                $mappedResults[] = $result;
            }
        }
        return $mappedResults[array_rand($mappedResults)];
    }
}
