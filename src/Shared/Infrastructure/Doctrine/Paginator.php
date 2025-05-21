<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\Repository\PaginatorInterface;
use Pagerfanta\PagerfantaInterface;

/**
 * @template T of object
 *
 * @implements PaginatorInterface<T>
 */
final readonly class Paginator implements PaginatorInterface
{
    final public const PAGE_SIZE = 10;

    /**
     * @param PagerfantaInterface<T> $paginator
     */
    public function __construct(private PagerfantaInterface $paginator)
    {
    }

    public function getItemsPerPage(): int
    {
        return $this->paginator->getMaxPerPage();
    }

    public function getCurrentPage(): int
    {
        return $this->paginator->getCurrentPage();
    }

    public function getLastPage(): int
    {
        // Pagerfanta getNbPages can return 0 if no results, ensure it's at least 1 for getLastPage logic
        return $this->paginator->getNbPages() ?: 1;
    }

    public function getTotalItems(): int
    {
        return $this->paginator->getNbResults(); // Total items in the whole set
    }

    public function count(): int
    {
        // Nombre d'éléments sur la page actuelle
        $currentPageResults = $this->paginator->getCurrentPageResults();
        if ($currentPageResults instanceof \Countable || \is_array($currentPageResults)) {
            return \count($currentPageResults);
        }

        // Fallback si ce n'est pas directement comptable (devrait l'être pour les adaptateurs Doctrine)
        return iterator_count($this->getIterator());
    }

    public function getIterator(): \Traversable
    {
        return $this->paginator->getIterator();
    }

    public function hasToPaginate(): bool
    {
        return $this->paginator->haveToPaginate();
    }

    public function hasPreviousPage(): bool
    {
        return $this->paginator->hasPreviousPage();
    }

    public function getPreviousPage(): int
    {
        return $this->paginator->getPreviousPage();
    }

    public function hasNextPage(): bool
    {
        return $this->paginator->hasNextPage();
    }

    public function getNextPage(): int
    {
        return $this->paginator->getNextPage();
    }

    public function getPagerfanta(): PagerfantaInterface
    {
        return $this->paginator;
    }
}
