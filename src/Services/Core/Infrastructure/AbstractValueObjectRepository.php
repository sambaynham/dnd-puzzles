<?php

declare(strict_types=1);

namespace App\Services\Core\Infrastructure;

use App\Services\Core\Domain\AbstractValueObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
/**
* @template T of ServiceEntityRepository
* @template-extends ServiceEntityRepository<T>
*/
abstract class AbstractValueObjectRepository extends ServiceEntityRepository
{
    /**
     * @param string $handle
     * @return ?T
     */
    final public function findByHandle(string $handle):  mixed {
        return self::mapResult($this->createQueryBuilder('v')
            ->where('v.handle = :handle')
            ->setParameter('handle', $handle)
            ->getQuery()
            ->getOneOrNullResult());
    }

    /**
     * @return ?T
     */
    private static function mapResult(mixed $result): mixed {
        if ($result instanceof AbstractValueObject) {
            return $result;
        }
        return null;
    }
}
