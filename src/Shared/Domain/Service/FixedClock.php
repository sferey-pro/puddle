<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

/**
 * Implémentation de ClockInterface qui retourne toujours une heure fixe.
 * Utile principalement pour les tests afin de contrôler le temps.
 */
final class FixedClock implements ClockInterface
{
    public function __construct(
        private readonly \DateTimeImmutable $frozenTime,
    ) {
    }

    /**
     * Retourne toujours la date et l'heure qui ont été "figées" lors de l'instanciation.
     */
    public function now(): \DateTimeImmutable
    {
        return $this->frozenTime;
    }
}
