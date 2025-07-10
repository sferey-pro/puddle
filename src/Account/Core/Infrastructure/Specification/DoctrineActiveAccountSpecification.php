<?php

namespace Account\Core\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;
use Kernel\Infrastructure\Specification\DoctrineSpecificationAdapter;
use Account\Core\Domain\Specification\ActiveAccountSpecification;

final class DoctrineActiveAccountSpecification extends DoctrineSpecificationAdapter
{
    public function __construct(
        private readonly ActiveAccountSpecification $specification
    ) {
    }

    public function getParameterName(): string
    {
        return 'active_account';
    }

    public function failureReason(): ?string {
        return $this->specification->failureReason();
    }

    public function modifyQuery(QueryBuilder $qb, string $alias): void
    {
        $qb->andWhere("{$alias}.active = :active")
           ->andWhere("{$alias}.suspendedAt IS NULL")
           ->andWhere("{$alias}.verifiedAt IS NOT NULL")
           ->setParameter('active', true);
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $this->specification->isSatisfiedBy($candidate);
    }
}
