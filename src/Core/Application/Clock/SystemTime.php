<?php

declare(strict_types=1);

namespace App\Core\Application\Clock;

/**
 * Fournit un point d'accès statique et global à l'horloge de l'application.
 *
 * Cette façade centralise la gestion du temps et le rend universellement accessible
 * à travers toutes les couches, y compris les entités et événements du Domaine.
 *
 * Elle utilise un mécanisme à double initialisation pour être robuste :
 * 1. Un "fournisseur par défaut" pour une initialisation paresseuse et sûre.
 * 2. Une méthode `set()` pour une injection explicite dans des contextes spécifiques (requête, test).
 */
final class SystemTime
{
    /**
     * @var clockInterface|null L'instance d'horloge actuellement active
     */
    private static ?ClockInterface $clock = null;

    /**
     * @var \Closure|null le fournisseur (factory) capable de créer une instance de l'horloge par défaut
     */
    private static ?\Closure $defaultProvider = null;

    /**
     * Enregistre la logique de création pour l'horloge par défaut.
     *
     * Cette méthode est appelée au démarrage de l'application (via le Kernel) pour
     * définir comment la façade doit instancier une horloge si aucune n'est explicitement fournie.
     *
     * @param \Closure $provider une fonction anonyme qui retourne une instance de ClockInterface
     */
    public static function setDefaultClockProvider(\Closure $provider): void
    {
        self::$defaultProvider = $provider;
    }

    /**
     * Définit explicitement l'horloge à utiliser pour le contexte courant.
     *
     * Permet de surcharger à l'exécution l'horloge par défaut. Utilisé par le
     * `SystemTimeInitializerSubscriber` ou durant les tests pour injecter une `FixedClock`.
     *
     * @param clockInterface $clock L'instance d'horloge à rendre active
     */
    public static function set(ClockInterface $clock): void
    {
        self::$clock = $clock;
    }

    /**
     * Récupère le moment présent depuis l'horloge active.
     *
     * Garantit qu'une horloge est toujours disponible en utilisant l'instance
     * explicitement définie, ou en en créant une à la demande via le fournisseur par défaut.
     *
     * @throws \LogicException si aucune horloge n'est définie et qu'aucun fournisseur par défaut n'est configuré
     */
    public static function now(): \DateTimeImmutable
    {
        if (null === self::$clock) {
            if (null === self::$defaultProvider) {
                throw new \LogicException('The SystemTime clock is not initialized and no default provider is configured. Please call SystemTime::setDefaultClockProvider() at application startup.');
            }
            // Exécute la closure fournie pour créer et assigner l'horloge par défaut.
            self::$clock = (self::$defaultProvider)();
        }

        return self::$clock->now();
    }

    /**
     * Réinitialise l'état interne de la façade.
     *
     * Principalement utilisé dans les environnements de test pour restaurer un état initial
     * et garantir l'isolation entre les cas de test.
     */
    public static function reset(): void
    {
        self::$clock = null;
    }
}
