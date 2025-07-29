<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Repository;

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Kernel\Domain\Saga\SagaStateId;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository pour le processus de Saga d'inscription.
 *
 * PHILOSOPHIE : Gestion du cycle de vie des Sagas
 * - Tracking de l'état du processus d'inscription
 * - Recherche par différents critères
 * - Support des états du workflow
 */
interface RegistrationProcessRepositoryInterface
{
    // ==================== CRUD BASIQUE ====================

    /**
     * Persiste un processus de Saga.
     */
    public function save(RegistrationSagaProcess $process): void;

    /**
     * Supprime un processus de Saga.
     */
    public function remove(RegistrationSagaProcess $process): void;

    // ==================== RECHERCHES ESSENTIELLES ====================

    /**
     * Trouve un processus par son ID de Saga.
     * Cas d'usage : Récupération dans RegistrationSaga après événement
     */
    public function findById(SagaStateId $sagaStateId): ?RegistrationSagaProcess;

    /**
     * Trouve un processus par UserId.
     * Cas d'usage : Vérifier si une inscription est déjà en cours
     */
    public function findByUserId(UserId $userId): ?RegistrationSagaProcess;

    /**
     * Trouve un processus actif par identifier.
     * Cas d'usage : Éviter les inscriptions multiples avec même email/phone
     */
    public function findActiveByIdentifier(string $identifierValue): ?RegistrationSagaProcess;

    // ==================== REQUÊTES MÉTIER ====================

    /**
     * Trouve tous les processus dans un état donné.
     * Cas d'usage : Monitoring, reprise après erreur
     *
     * @param string $state État du workflow (started, account_created, etc.)
     * @return RegistrationSagaProcess[]
     */
    public function findByState(string $state): array;

    /**
     * Trouve les processus bloqués (non terminés depuis X temps).
     * Cas d'usage : Détection des Sagas en échec, alerting
     *
     * @param \DateInterval $stuckSince Durée depuis laquelle considérer comme bloqué
     * @return RegistrationSagaProcess[]
     */
    public function findStuckProcesses(\DateInterval $stuckSince): array;

    /**
     * Compte les processus actifs.
     * Cas d'usage : Métriques, monitoring charge système
     */
    public function countActive(): int;

    // ==================== MAINTENANCE ====================

    /**
     * Nettoie les anciens processus terminés.
     * Cas d'usage : Maintenance DB, RGPD
     *
     * @param \DateTimeInterface $before Supprimer avant cette date
     * @return int Nombre de processus supprimés
     */
    public function cleanupCompleted(\DateTimeInterface $before): int;
}
