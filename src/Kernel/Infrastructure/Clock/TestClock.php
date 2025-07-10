<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Clock;

use Kernel\Application\Clock\ClockInterface;

/**
 * Horloge configurable pour les tests.
 * Permet de contrôler le temps dans les tests automatisés.
 */
final class TestClock implements ClockInterface
{
    private \DateTimeImmutable $currentTime;
    private bool $frozen;

    public function __construct(?\DateTimeImmutable $startTime = null)
    {
        $this->currentTime = $startTime ?? new \DateTimeImmutable();
        $this->frozen = false;
    }

    public function now(): \DateTimeImmutable
    {
        if (!$this->frozen) {
            // Si non gelé, retourne le temps réel
            return new \DateTimeImmutable();
        }

        return $this->currentTime;
    }

    /**
     * Fixe le temps à une date précise.
     */
    public function setTo(\DateTimeImmutable $time): void
    {
        $this->currentTime = $time;
        $this->frozen = true;
    }

    /**
     * Avance le temps de la durée spécifiée.
     */
    public function advance(\DateInterval $interval): void
    {
        $this->currentTime = $this->currentTime->add($interval);
    }

    /**
     * Recule le temps de la durée spécifiée.
     */
    public function rewind(\DateInterval $interval): void
    {
        $this->currentTime = $this->currentTime->sub($interval);
    }

    /**
     * Gèle le temps à l'instant actuel.
     */
    public function freeze(): void
    {
        $this->frozen = true;
        $this->currentTime = new \DateTimeImmutable();
    }

    /**
     * Dégèle le temps (retour au temps réel).
     */
    public function unfreeze(): void
    {
        $this->frozen = false;
    }

    /**
     * Réinitialise l'horloge.
     */
    public function reset(): void
    {
        $this->currentTime = new \DateTimeImmutable();
        $this->frozen = false;
    }
}
