<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

/**
 * Façade statique pour accéder à l'horloge.
 */
final class SystemTime
{
    private static ?ClockInterface $clock = null;

    public static function set(ClockInterface $clock): void
    {
        self::$clock = $clock;
    }

    public static function now(): \DateTimeImmutable
    {
        if (self::$clock === null) {
            // Cette exception garantit que l'horloge est toujours initialisée.
            throw new \LogicException('The SystemTime clock has not been initialized. Please call SystemTime::set() at application startup.');
        }

        return self::$clock->now();
    }
}
