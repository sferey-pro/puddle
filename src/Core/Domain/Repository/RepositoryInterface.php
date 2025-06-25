<?php

declare(strict_types=1);

namespace App\Core\Domain\Repository;

/**
 * @template T of object
 *
 * @extends \IteratorAggregate<array-key, T>
 */
interface RepositoryInterface extends \IteratorAggregate, \Countable
{
    /**
     * @return \Iterator<T>
     */
    public function getIterator(): \Iterator;

    public function count(): int;

    /**
     * @return PaginatorInterface<T>|null
     */
    public function paginator();

    /**
     * @return static<T>
     */
    public function withPagination(int $page, int $itemsPerPage): static;

    /**
     * @return static<T>
     */
    public function withoutPagination(): static;
}
