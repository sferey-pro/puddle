<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Doctrine\Repository\PaginatorInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * @template T of object
 *
 * @implements PaginatorInterface<T>
 */
final readonly class Paginator implements PaginatorInterface
{
    final public const PAGE_SIZE = 10;

    private int $firstResult;
    private int $maxResults;

    /**
     * @param DoctrinePaginator<T> $paginator
     */
    public function __construct(
        private DoctrinePaginator $paginator,
    ) {
        $firstResult = $paginator->getQuery()->getFirstResult();
        $maxResults = $paginator->getQuery()->getMaxResults();

        if (null === $maxResults) {
            throw new \InvalidArgumentException('Missing maxResults from the query.');
        }

        $this->firstResult = $firstResult;
        $this->maxResults = $maxResults;
    }

    public function getItemsPerPage(): int
    {
        return $this->maxResults;
    }

    public function getCurrentPage(): int
    {
        if (0 >= $this->maxResults) {
            return 1;
        }

        return (int) (1 + floor($this->firstResult / $this->maxResults));
    }

    public function getLastPage(): int
    {
        if (0 >= $this->maxResults) {
            return 1;
        }

        return (int) (ceil($this->getTotalItems() / $this->maxResults) ?: 1);
    }

    public function getTotalItems(): int
    {
        return $this->paginator->count();
    }

    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    public function getIterator(): \Traversable
    {
        return $this->paginator->getIterator();
    }

    public function hasToPaginate(): bool
    {
        return $this->getTotalItems() > $this->maxResults;
    }

    public function hasPreviousPage(): bool
    {
        return $this->getCurrentPage() > 1;
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->getCurrentPage() - 1);
    }

    public function hasNextPage(): bool
    {
        return $this->getCurrentPage() < $this->getLastPage();
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->getCurrentPage() + 1);
    }

    /**
     * @return \Traversable<int, object>
     */
    public function getResults(): \Traversable
    {
        return $this->paginator->getIterator();
    }
}
