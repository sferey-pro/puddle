<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence;

use App\Core\Domain\Repository\PaginatorInterface;
use App\Core\Domain\Repository\RepositoryInterface;
use App\Core\Infrastructure\Persistence\Paginator\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class ORMAbstractRepository extends ServiceEntityRepository implements RepositoryInterface
{
    use DoctrineRepositoryTrait;

    private ?int $page = null;
    private ?int $itemsPerPage = Paginator::PAGE_SIZE;

    private QueryBuilder $queryBuilder;

    public function __construct(
        ManagerRegistry $registry,
        string $entityClass,
        string $alias,
    ) {
        parent::__construct($registry, $entityClass);

        $this->queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select($alias)
            ->from($entityClass, $alias);
    }

    public function paginator(): ?PaginatorInterface
    {
        if (null === $this->page || null === $this->itemsPerPage) {
            return null;
        }

        $maxResults = $this->itemsPerPage;

        /** @var array<string, mixed> $havingDqlParts */
        $havingDqlParts = $this->queryBuilder->getDQLPart('having');
        $useOutputWalkers = \count($havingDqlParts ?: []) > 0;

        $query = new QueryAdapter($this->query(), true, $useOutputWalkers);

        /** @var Pagerfanta<T> $paginator */
        $paginator = new Pagerfanta($query);

        $paginator->setMaxPerPage($maxResults);
        $paginator->setCurrentPage($this->page);

        return new Paginator($paginator);
    }

    public function getIterator(): \Iterator
    {
        if (null !== $paginator = $this->paginator()) {
            yield from $paginator;

            return;
        }

        yield from $this->queryBuilder->getQuery()->getResult();
    }

    protected function query(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }
}
