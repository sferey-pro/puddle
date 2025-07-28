<?php

declare(strict_types=1);

namespace Kernel\Application\Clock;

/**
 * Définit le contrat pour l'obtention du temps (Port).
 *
 * Cette interface est un "Port" au sens de l'Architecture Hexagonale. Elle permet au Domaine
 * de dépendre d'une abstraction du temps, plutôt que d'une implémentation concrète (comme `new \DateTimeImmutable()`).
 *
 * Cette abstraction est cruciale pour la testabilité, car elle permet d'injecter des horloges
 * alternatives (ex: une horloge fixe) pour rendre les tests déterministes.
 */
interface ClockInterface
{
    /**
     * Récupère le moment présent.
     *
     * @return \DateTimeImmutable une représentation immuable du temps actuel
     */
    public function now(): \DateTimeImmutable;
}
