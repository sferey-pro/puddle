<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kernel\Domain\ValueObject\AggregateRootId;

/**
 * Common repository operations for Doctrine implementations.
 *
 * @template T of object
 * @template TId of AggregateRootId
 *
 * @property EntityManagerInterface $em
 * @property EntityRepository<T> $repository
 * @property class-string<T> $entityClass
 */
trait DoctrineRepositoryTrait
{
    /**
     * @param T $aggregateRoot
     * @return void
     */
    public function save(object $aggregateRoot): void
    {
        $this->em->persist($aggregateRoot);
    }

    /**
     * @param T $aggregateRoot
     * @return void
     */
    public function remove(object $aggregateRoot): void
    {
        $this->em->remove($aggregateRoot);
    }

    /**
     * @param TId $id
     * @return T|null
     */
    public function ofId(AggregateRootId $id): ?object
    {
        /** @var T|null */
        return $this->repository->find($id);
    }

    /**
     * @param TId $id
     * @return bool
     */
    public function exists(AggregateRootId $id): bool
    {
        return $this->ofId($id) !== null;
    }
}
