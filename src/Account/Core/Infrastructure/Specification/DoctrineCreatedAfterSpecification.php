<?php

namespace Account\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;
use Kernel\Infrastructure\Specification\DoctrineSpecificationAdapter;

final class DoctrineCreatedAfterSpecification extends DoctrineSpecificationAdapter
{
    public function __construct(
        private readonly \DateTimeImmutable $date
    ) {
    }

    public function failureReason(): ?string {
        return null;
    }

    public function modifyQuery(QueryBuilder $qb, string $alias): void
    {
        $paramName = $this->getParameterName();

        $qb->andWhere("{$alias}.createdAt > :{$paramName}")
           ->setParameter($paramName, $this->date);
    }

    public function getParameterName(): string
    {
        return 'created_after_' . $this->date->getTimestamp();
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $candidate->getCreatedAt() > $this->date;
    }
}
