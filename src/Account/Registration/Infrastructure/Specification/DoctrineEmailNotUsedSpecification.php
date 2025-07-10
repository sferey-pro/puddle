<?php

namespace Account\Registration\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;
use Kernel\Infrastructure\Specification\DoctrineSpecificationAdapter;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;

final class DoctrineEmailNotUsedSpecification extends DoctrineSpecificationAdapter
{
    public function __construct(
        private readonly EmailAddress $email
    ) {
    }

    public function getParameterName(): string {
        return 'email_not_used';
    }

    public function failureReason(): ?string {
        return null;
    }

    public function modifyQuery(QueryBuilder $qb, string $alias): void
    {
        $qb->select("COUNT({$alias}.id)")
           ->where("{$alias}.email = :email")
           ->setParameter('email', $this->email);
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        // Pour Doctrine, on vérifie via le count
        return true; // La logique est dans le repository
    }
}
