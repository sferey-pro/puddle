<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Persistence\Doctrine\Specification;

use Doctrine\ORM\QueryBuilder;
use Identity\Domain\Specification\IsUniqueIdentitySpecification;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Infrastructure\Specification\DoctrineSpecificationAdapter;

final class DoctrineIsUniqueIdentitySpecification extends DoctrineSpecificationAdapter
{
    public function __construct(
        private readonly IsUniqueIdentitySpecification $specification
    ) {
    }

    public function getParameterName(): string {
        return 'is_unique_identity';
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return $this->specification->isSatisfiedBy($candidate);
    }

    public function failureReason(): ?string {
        return $this->specification->failureReason();
    }

    /**
     * @param IsUniqueIdentitySpecification $specification
     */
    public function modifyQuery(QueryBuilder $qb, string $alias): void
    {
        $identifier = $this->specification->identifier;

        match ($identifier::class) {
            EmailIdentity::class => $qb->andWhere(sprintf('%s.email_address = :identityValue', $alias))
                                       ->setParameter('identityValue', $identifier->email),

            PhoneIdentity::class => $qb->andWhere(sprintf('%s.phone_number = :identityValue', $alias))
                                       ->setParameter('identityValue', $identifier->phone),

            default => throw new \InvalidArgumentException('Unsupported Identifier type for specification.')
        };
    }


}
