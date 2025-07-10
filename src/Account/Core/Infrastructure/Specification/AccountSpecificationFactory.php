<?php

namespace Account\Core\Infrastructure\Specification;

final class AccountSpecificationFactory
{
    /**
     * Crée les specifications Doctrine appropriées selon le contexte.
     */
    public function createForRepository(string $context): array
    {
        return match ($context) {
            'active_accounts' => [
                new DoctrineActiveAccountSpecification(),
                new DoctrineVerifiedAccountSpecification()
            ],
            'deletable_accounts' => [
                new DoctrineInactiveForPeriodSpecification(days: 90),
                new DoctrineNoRecentActivitySpecification()
            ],
            default => throw new \InvalidArgumentException("Unknown context: {$context}")
        };
    }
}
