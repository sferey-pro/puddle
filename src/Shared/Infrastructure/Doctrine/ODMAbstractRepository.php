<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\Repository\PaginatorInterface;
use App\Shared\Domain\Repository\RepositoryInterface;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;
use Doctrine\Bundle\MongoDBBundle\Repository\ServiceDocumentRepository;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Pagerfanta\Doctrine\MongoDBODM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class ODMAbstractRepository extends ServiceDocumentRepository implements RepositoryInterface
{
    use DoctrineRepositoryTrait;

    private ?int $page = null;
    private ?int $itemsPerPage = Paginator::PAGE_SIZE;

    private QueryBuilder $queryBuilder;

    public function __construct(
        ManagerRegistry $registry,
        string $documentClass,
    ) {
        parent::__construct($registry, $documentClass);

        $this->queryBuilder = $this->getDocumentManager()->createQueryBuilder($documentClass);
    }

    public function paginator(): ?PaginatorInterface
    {
        if (null === $this->page || null === $this->itemsPerPage) {
            return null;
        }

        $query = new QueryAdapter($this->query());

        /** @var Pagerfanta<T> $paginator */
        $paginator = new Pagerfanta($query);

        $paginator->setMaxPerPage($this->itemsPerPage);
        $paginator->setCurrentPage($this->page);

        return new Paginator($paginator);
    }

    public function getIterator(): \Iterator
    {
        if (null !== $paginator = $this->paginator()) {
            yield from $paginator;

            return;
        }

        yield from $this->queryBuilder->getQuery()->execute();
    }

    protected function query(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }

    public function count(): int
    {
        return $this->paginator()->count();
    }
}
