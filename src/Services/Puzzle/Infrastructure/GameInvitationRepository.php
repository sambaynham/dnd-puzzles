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
            ->where($qb->expr()->eq('gig.id', ':gameId'))
            ->andWhere($qb->expr()->gt('gi.expiresAt', ':now'))
            ->andWhere($qb->expr()->isNull('gi.dateUsed'));
        $qb->setParameter('now', new \DateTimeImmutable());
        $qb->setParameter(':gameId', $game->getId());

        $results = $qb->getQuery()->getArrayResult();
        return new ArrayCollection($results);
    }

    public function findByInvitationCode(string $invitationCode): ? GameInvitation {
        $qb = $this->createQueryBuilder('gi');

        $qb ->where($qb->expr()->eq('gi.invitationCode', ':invitationCode'))
            ->andWhere($qb->expr()->gt('gi.expiresAt', ':now'))
            ->andWhere($qb->expr()->isNull('gi.dateUsed'));
        $qb->setParameter('now', new \DateTimeImmutable());
        $qb->setParameter(':invitationCode', $invitationCode);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getExpiredInvitations(): array {
        $qb = $this->createQueryBuilder('gi');
        $qb->where($qb->expr()->lte('gi.expiresAt', ':now'));
        $qb->setParameter(':now', new \DateTimeImmutable());
        return $qb->getQuery()->getResult();
    }
}
