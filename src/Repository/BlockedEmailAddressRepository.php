<?php

namespace App\Repository;

use App\Entity\BlockedEmailAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BlockedEmailAddress>
 */
class BlockedEmailAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlockedEmailAddress::class);
    }
    public function findByEmail(string $email): ?BlockedEmailAddress {
        return $this->findOneBy(['emailAddress' => $email]);
    }
}
