<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Service\ClockInterface;
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
