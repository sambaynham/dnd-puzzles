<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Infrastructure\Casebook;

use App\Services\Puzzle\Domain\Casebook\Casebook;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Casebook>
 */
class CasebookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casebook::class);
    }


    public function findBySlug(string $slug): ? Casebook {
        return $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
