<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Infrastructure\Casebook;

use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Domain\Interfaces\StaticPuzzleInstanceProviderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Casebook>
 */
class CasebookRepository extends ServiceEntityRepository implements StaticPuzzleInstanceProviderInterface
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

    public function decollideSlug(string $candidateSlug, int $attempts = 0): string {

        if (null === $this->findBySlug($candidateSlug)) {
            return $candidateSlug;
        }

        $attempts++;
        $newCandidateSlug = sprintf("%s_%d", $candidateSlug, $attempts);
        if (null === $this->findBySlug($newCandidateSlug)) {
            return $newCandidateSlug;
        } else {
            return $this->decollideSlug($candidateSlug, $attempts);
        }
    }

    public function getStaticPuzzleInstancesForGame(Game $game): Collection
    {
        $qb = $this->createQueryBuilder('cp')
            ->where('g = :game')
            ->innerJoin('cp.game', 'g')
            ->setParameter(':game', $game);

        return new ArrayCollection($qb->getQuery()->getResult());
    }

    public function providesTemplateInstances(): string
    {
        return Casebook::TEMPLATE_SLUG;
    }
}
