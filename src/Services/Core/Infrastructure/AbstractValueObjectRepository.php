<?php

declare(strict_types=1);

namespace App\Services\Core\Infrastructure;

use App\Services\Core\Domain\AbstractValueObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
/**
 * @template-extends  ServiceEntityRepository<AbstractValueObject>
 */
abstract class AbstractValueObjectRepository extends ServiceEntityRepository
{
    /**
     * @param string $handle
     * @return ?AbstractValueObject
     */
    final public function findByHandle(string $handle): ? AbstractValueObject {
        return self::mapResult($this->createQueryBuilder('v')
            ->where('v.handle = :handle')
            ->setParameter('handle', $handle)
            ->getQuery()
            ->getOneOrNullResult());
    }

    private static function mapResult(mixed $result): ? AbstractValueObject {
        if ($result instanceof AbstractValueObject) {
            return $result;
        }
        return null;
    }
}
