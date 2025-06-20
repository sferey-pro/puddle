<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;

/**
 * Implémentation de ClockInterface retournant un temps figé (frozen time).
 *
 * Cet objet est un "Test Double" (spécifiquement un "Stub"). Son unique but est de fournir une valeur
 * constante et prédictible pour le temps dans les tests unitaires et fonctionnels.
 * Il permet de vérifier le comportement du domaine à un instant T précis.
 */
final class FixedClock implements ClockInterface
{
    /**
     * @param \DateTimeImmutable $frozenTime le moment précis auquel le temps sera figé
     */
    public function __construct(
        private readonly \DateTimeImmutable $frozenTime,
    ) {
    }

    /**
     * Retourne systématiquement la date et l'heure qui ont été définies lors de l'instanciation.
     *
     * @return \DateTimeImmutable le temps figé
     */
    public function now(): \DateTimeImmutable
    {
        return $this->frozenTime;
    }
}
