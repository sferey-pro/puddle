<?php

namespace Account\Registration\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;
use Kernel\Infrastructure\Specification\DoctrineSpecificationAdapter;

/**
 * Filtre les comptes créés pendant les périodes d'inscription ouvertes.
 * Utile pour les rapports et analyses.
 */
final class DoctrineRegistrationOpenSpecification extends DoctrineSpecificationAdapter
{
    public function __construct(
        private readonly array $openPeriods // Périodes où l'inscription était ouverte
    ) {
    }

    public function failureReason(): ?string {
        return null;
    }

    public function modifyQuery(QueryBuilder $qb, string $alias): void
    {
        $orConditions = $qb->expr()->orX();

        foreach ($this->openPeriods as $index => $period) {
            $startParam = "period_start_{$index}";
            $endParam = "period_end_{$index}";

            $orConditions->add(
                $qb->expr()->between(
                    "{$alias}.createdAt",
                    ":{$startParam}",
                    ":{$endParam}"
                )
            );

            $qb->setParameter($startParam, $period['start']);
            $qb->setParameter($endParam, $period['end']);
        }

        $qb->andWhere($orConditions);
    }

    public function getParameterName(): string
    {
        return 'registration_open_periods';
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        // Vérifie si le compte a été créé pendant une période ouverte
        $createdAt = $candidate->getCreatedAt();

        foreach ($this->openPeriods as $period) {
            if ($createdAt >= $period['start'] && $createdAt <= $period['end']) {
                return true;
            }
        }

        return false;
    }
}
