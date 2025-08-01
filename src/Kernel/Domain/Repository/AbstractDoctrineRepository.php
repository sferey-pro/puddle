<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Persistence\Doctrine\Repository;

use Kernel\Domain\Repository\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kernel\Domain\ValueObject\AggregateRootId;

/**
 * Base Doctrine repository with extends ServiceEntityRepository.
 *
 * @template T of object
 * @template TId of AggregateRootId
 *
 * @implements RepositoryInterface<T, TId>
 */
abstract class AbstractDoctrineRepository extends ServiceEntityRepository implements RepositoryInterface
{
    /** @use DoctrineRepositoryTrait<T, TId> */
    use DoctrineRepositoryTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, static::class);
    }
}
