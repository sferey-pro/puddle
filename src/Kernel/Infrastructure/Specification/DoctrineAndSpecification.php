<?php

namespace Kernel\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;

final class DoctrineAndSpecification extends DoctrineSpecificationAdapter
{
    public function __construct(
        private readonly DoctrineSpecificationAdapter $left,
        private readonly DoctrineSpecificationAdapter $right
    ) {
    }

    public function failureReason(): ?string {
        return null;
    }

    public function modifyQuery(QueryBuilder $qb, string $alias): void
    {
        $this->left->modifyQuery($qb, $alias);
        $this->right->modifyQuery($qb, $alias);
    }

    public function getParameterName(): string
    {
        return 'and_' . spl_object_id($this);
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $this->left->isSatisfiedBy($candidate)
            && $this->right->isSatisfiedBy($candidate);
    }
}
