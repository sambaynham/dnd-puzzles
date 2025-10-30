<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function findAllPaginated(int $firstResult, int $maxResults = 50): iterable {
        $qb = $this->createQueryBuilder('u')
            ->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy('u.createdAt', 'ASC');

        return new Paginator($qb->getQuery());
    }

    public function searchByEmailOrUserName(string $searchTerms, int $firstResult, int $maxResults = 50): iterable {
        $qb = $this->createQueryBuilder('u');
            $qb->setFirstResult($firstResult)
            ->setMaxResults($maxResults)
            ->orderBy('u.createdAt', 'ASC');

        $terms = explode(' ', $searchTerms);

        for ($i = 0; $i < count($terms); $i++) {
            $qb->orWhere($qb->expr()->like('u.username', ':term'.$i));
            $qb->orWhere($qb->expr()->like('u.email', ':term'.$i));
            //TODO - Fix this, then think about what you did.
            $qb->setParameter(sprintf('term%d', $i), '%'.$terms[$i].'%');
        }
        return new Paginator($qb->getQuery());
    }
    public function getUsersCount(): int {
        return $this->createQueryBuilder('u')
            ->select('count(u.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
