<?php

namespace App\Services\Puzzle\Infrastructure;

use App\Entity\Game;
use App\Entity\GameInvitation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<GameInvitation>
 */
class GameInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameInvitation::class);
    }

    public function getOutstandingInvitationsForGame(Game $game) : iterable {

        $qb = $this->createQueryBuilder('gi');

        $qb->innerJoin('gi.game', 'gig')
            ->where($qb->expr()->eq('gig.id', $game->getId()))
            ->andWhere($qb->expr()->lt('gi.expiresAt', ':now'))
            ->andWhere($qb->expr()->isNull('gi.dateUsed'));
        $qb->setParameter('now', new \DateTimeImmutable());

        $results = $qb->getQuery()->getArrayResult();
        return new ArrayCollection($results);
    }

}
