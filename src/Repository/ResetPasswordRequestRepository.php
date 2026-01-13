<?php

namespace App\Repository;

use App\Entity\ResetPasswordRequest;
use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordRequestInterface;
use SymfonyCasts\Bundle\ResetPassword\Persistence\Repository\ResetPasswordRequestRepositoryTrait;
use SymfonyCasts\Bundle\ResetPassword\Persistence\ResetPasswordRequestRepositoryInterface;

/**
 * @extends ServiceEntityRepository<ResetPasswordRequest>
 */
class ResetPasswordRequestRepository extends ServiceEntityRepository implements ResetPasswordRequestRepositoryInterface
{
    use ResetPasswordRequestRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResetPasswordRequest::class);
    }

    /**
     * @param object $user
     * @param \DateTimeInterface $expiresAt
     * @param string $selector
     * @param string $hashedToken
     * @return ResetPasswordRequestInterface
     */
    public function createResetPasswordRequest(
        object $user,
        \DateTimeInterface $expiresAt,
        string $selector,
        string $hashedToken
    ): ResetPasswordRequestInterface
    {
        if (!$user instanceof User) {
            throw new \InvalidArgumentException('The user must be an instance of UserInterface.');
        }
        return new ResetPasswordRequest(
            user: $user,
            expiresAt: $expiresAt,
            selector: $selector,
            hashedToken: $hashedToken
        );
    }

    /**
     * @return ResetPasswordRequest[]
     */
    public function findAllForUser(User $user): array {
        $qb = $this->createQueryBuilder('pr');
        $qb->where($qb->expr()->eq('pr.user', ':user'));
        $qb->setParameter('user', $user);
        $results = $qb->getQuery()->getResult();
        return self::mapArrayResults($results);
    }

    /**
     * @param array $results
     * @return ResetPasswordRequest[]
     */
    private static function mapArrayResults(array $results): array {
        $mappedResults = [];
        foreach ($results as $result) {
            if ($result instanceof ResetPasswordRequest) {
                $mappedResults[] = $result;
            }
        }
        return $mappedResults;
    }
}
