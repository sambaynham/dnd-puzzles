<?php

declare(strict_types=1);

namespace App\Services\User\Infrastructure\Repository;

use App\Services\Core\Infrastructure\AbstractValueObjectRepository;
use App\Services\User\Domain\Permission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Permission>
 */
class PermissionRepository extends AbstractValueObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Permission::class);
    }
}
