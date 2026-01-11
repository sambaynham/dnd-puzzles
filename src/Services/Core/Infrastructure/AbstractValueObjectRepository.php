<?php

declare(strict_types=1);

namespace App\Services\Core\Infrastructure;

use App\Services\Core\Domain\AbstractValueObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
/**
 * @template-extends  ServiceEntityRepository<T>
 */
abstract class AbstractValueObjectRepository extends ServiceEntityRepository
{
    /**
     * @param string $handle
     * @return <T>|null
     */
    final public function findByHandle(string $handle): ? AbstractValueObject {
        return $this->createQueryBuilder('v')
            ->where('v.handle = :handle')
            ->setParameter('handle', $handle)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
