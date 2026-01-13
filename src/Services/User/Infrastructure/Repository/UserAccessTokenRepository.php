<?php

declare(strict_types=1);

namespace App\Services\User\Infrastructure\Repository;

use App\Services\User\Domain\User;
use App\Services\User\Domain\UserAccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<UserAccessToken>
 */
class UserAccessTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAccessToken::class);
    }

    public function findOneByToken(string $token): ?UserAccessToken {
        return $this->findOneBy(['token' => $token]);
    }
}
