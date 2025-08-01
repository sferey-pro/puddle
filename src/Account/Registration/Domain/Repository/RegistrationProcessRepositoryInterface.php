<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Repository;

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Repository\RepositoryInterface;
use Kernel\Domain\Saga\SagaStateId;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository pour le processus de Saga d'inscription.
 *
 * PHILOSOPHIE : Gestion du cycle de vie des Sagas
 * - Tracking de l'état du processus d'inscription
 * - Recherche par différents critères
 * - Support des états du workflow
 *
 * @extends RepositoryInterface<RegistrationSagaProcess, SagaStateId>
 */
interface RegistrationProcessRepositoryInterface extends RepositoryInterface
{
    // Recherche par critère unique
    // ============================

    /**
     * Trouve un processus par UserId.
     */
    public function ofUserId(UserId $id): ?RegistrationSagaProcess;

    /**
     * Trouve un processus par Identifier.
     */
    public function ofIdentifier(Identifier $identifier): ?RegistrationSagaProcess;

    // Recherche multiple
    // ==================

    /**
     * Trouve tous les processus dans un état donné.
     * Cas d'usage : Monitoring, reprise après erreur
     *
     * @param string $state État du workflow (started, account_created, etc.)
     * @return RegistrationSagaProcess[]
     */
    public function allofState(string $state): array;

    /**
     * Trouve les processus bloqués (non terminés depuis X temps).
     * Cas d'usage : Détection des Sagas en échec, alerting
     *
     * @param \DateInterval $stuckSince Durée depuis laquelle considérer comme bloqué
     * @return RegistrationSagaProcess[]
     */
    public function allStuckProcesses(\DateInterval $stuckSince): array;

    // Spécifique métier
    // =================

    /**
     * Trouve un processus actif par identifier.
     * Cas d'usage : Éviter les inscriptions multiples avec même email/phone
     */
    public function findActiveByIdentifier(Identifier $identifier): ?RegistrationSagaProcess;

    /**
     * Compte les processus actifs.
     * Cas d'usage : Métriques, monitoring charge système
     */
    public function countActive(): int;

    // Spécifique system
    // =================

    /**
     * Nettoie les anciens processus terminés.
     * Cas d'usage : Maintenance DB, RGPD
     *
     * @param \DateTimeInterface $before Supprimer avant cette date
     * @return int Nombre de processus supprimés
     */
    public function cleanupCompleted(\DateTimeInterface $before): int;
}
