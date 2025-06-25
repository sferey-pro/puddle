<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Clock;

use App\Core\Application\Clock\ClockInterface;
use Symfony\Component\Clock\Clock;

/**
 * Implémentation de l'horloge qui utilise le composant Clock de Symfony.
 */
final class SystemClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        return Clock::get()->now();
    }
}
