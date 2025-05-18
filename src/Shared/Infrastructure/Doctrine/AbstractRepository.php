<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine;

use App\Shared\Domain\Repository\PaginatorInterface;
use App\Shared\Domain\Repository\RepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use Webmozart\Assert\Assert;

/**
 * @template T of object
 *
 * @implements RepositoryInterface<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository implements RepositoryInterface
{
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

    public function getIterator(): \Iterator
    {
        if (null !== $paginator = $this->paginator()) {
            yield from $paginator;

            return;
        }

        yield from $this->queryBuilder->getQuery()->getResult();
    }

    public function paginator(): ?PaginatorInterface
    {
        if (null === $this->page || null === $this->itemsPerPage) {
            return null;
        }

        $firstResult = ($this->page - 1) * $this->itemsPerPage;
        $maxResults = $this->itemsPerPage;

        $repository = $this->filter(static function (QueryBuilder $qb) use ($firstResult, $maxResults) {
            $qb->setFirstResult($firstResult)->setMaxResults($maxResults);
        });

        /** @var DoctrinePaginator<T> $paginator */
        $paginator = new DoctrinePaginator($repository->queryBuilder->getQuery(), true);

        /** @var array<string, mixed> $havingDqlParts */
        $havingDqlParts = $this->queryBuilder->getDQLPart('having');

        $useOutputWalkers = \count($havingDqlParts ?: []) > 0;
        $paginator->setUseOutputWalkers($useOutputWalkers);

        return new Paginator($paginator);
    }

    public function withoutPagination(): static
    {
        $cloned = clone $this;
        $cloned->page = null;
        $cloned->itemsPerPage = null;

        return $cloned;
    }

    public function withPagination(int $page, int $itemsPerPage): static
    {
        Assert::positiveInteger($page);
        Assert::positiveInteger($itemsPerPage);

        $cloned = clone $this;
        $cloned->page = $page;
        $cloned->itemsPerPage = $itemsPerPage;

        return $cloned;
    }

    /**
     * @return static<T>
     */
    protected function filter(callable $filter): static
    {
        $cloned = clone $this;
        $filter($cloned->queryBuilder);

        return $cloned;
    }

    protected function query(): QueryBuilder
    {
        return clone $this->queryBuilder;
    }

    protected function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }
}
