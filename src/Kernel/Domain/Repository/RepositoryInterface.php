<?php

declare(strict_types=1);

namespace Kernel\Domain\Repository;

use Kernel\Domain\ValueObject\AggregateRootId;

/**
 * Base repository interface with common CRUD operations.
 *
 * @template T of object The aggregate root type
 * @template TId of AggregateRootId The aggregate root ID type
 */
interface RepositoryInterface
{
    /**
     * Persists an aggregate root.
     *
     * @param T $aggregateRoot
     * @return void
     */
    public function save(object $aggregateRoot): void;

    /**
     * Removes an aggregate root.
     *
     * @param T $aggregateRoot
     * @return void
     */
    public function remove(object $aggregateRoot): void;

    /**
     * Finds an aggregate root by its ID.
     *
     * @param TId $id
     * @return T|null
     */
    public function ofId(AggregateRootId $id): ?object;

    /**
     * Checks if an aggregate root exists.
     *
     * @param TId $id
     * @return bool
     */
    public function exists(AggregateRootId $id): bool;
}
