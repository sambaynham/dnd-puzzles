<?php

declare(strict_types=1);

namespace App\Services\User\Infrastructure\Repository;

use App\Services\Core\Infrastructure\AbstractValueObjectRepository;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractValueObjectRepository<UserAccountType>
 */
class UserAccountTypeRepository extends AbstractValueObjectRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAccountType::class);
    }
}
