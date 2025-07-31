<?php

declare(strict_types=1);

namespace Authentication\Domain\Repository;

use Authentication\Domain\Model\LoginAttempt;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository pour la gestion des tentatives de connexion.
 *
 * PHILOSOPHIE : Audit et Rate Limiting
 * - Traçabilité complète des tentatives
 * - Rate limiting par identifier ET par IP
 * - Analyse des patterns de sécurité
 */
interface LoginAttemptRepositoryInterface
{
    // ==================== CRUD BASIQUE ====================

    /**
     * Enregistre une tentative de connexion.
     */
    public function save(LoginAttempt $attempt): void;

    // ==================== RATE LIMITING ====================

    /**
     * Compte les tentatives échouées récentes pour un identifier.
     * Cas d'usage : Rate limiting par email/phone
     *
     * @param string $identifier Email ou phone
     * @param \DateInterval $period Période à analyser (ex: PT30M pour 30 minutes)
     * @return int Nombre de tentatives échouées
     */
    public function countFailedAttemptsByIdentifier(string $identifier, \DateInterval $period): int;

    /**
     * Compte les tentatives échouées récentes depuis une IP.
     * Cas d'usage : Blocage IP après X tentatives
     *
     * @param string $ipAddress Adresse IP
     * @param \DateInterval $period Période à analyser
     * @return int Nombre de tentatives échouées
     */
    public function countFailedAttemptsByIp(string $ipAddress, \DateInterval $period): int;

    /**
     * Récupère la dernière tentative réussie pour un identifier.
     * Cas d'usage : "Dernière connexion le..."
     */
    public function findLastSuccessfulByIdentifier(string $identifier): ?LoginAttempt;

    // ==================== HISTORIQUE & AUDIT ====================

    /**
     * Récupère l'historique des tentatives pour un utilisateur.
     * Cas d'usage : Dashboard sécurité utilisateur
     *
     * @param UserId $userId
     * @param int $limit Nombre max de résultats
     * @return LoginAttempt[]
     */
    public function findRecentByUserId(UserId $userId, int $limit = 10): array;

    /**
     * Récupère l'historique des tentatives pour un identifiant donné.
     * Cas d'usage : Dashboard sécurité utilisateur
     *
     * @param Identifier $identifier
     * @param int $windowMinutes
     * @return LoginAttempt[]
     */
    public function findRecentByIdentifier(Identifier $identifier, int $windowMinutes = 30): array;

    /**
     * Trouve les IPs suspectes (multiples échecs).
     * Cas d'usage : Dashboard admin sécurité
     *
     * @param \DateInterval $period Période d'analyse
     * @param int $threshold Seuil d'échecs pour être suspect
     * @return array<string, int> [ip => count]
     */
    public function findSuspiciousIps(\DateInterval $period, int $threshold = 5): array;

    /**
     * Récupère les tentatives récentes depuis une IP donnée.
     *
     * @return LoginAttempt[]
     */
    public function findRecentByIp(string $ipAddress, int $windowMinutes = 30): array;

    // ==================== MAINTENANCE ====================

    /**
     * Supprime les anciennes tentatives.
     * Cas d'usage : RGPD, nettoyage DB
     *
     * @param \DateTimeInterface $before Supprimer avant cette date
     * @return int Nombre d'enregistrements supprimés
     */
    public function removeOlderThan(\DateTimeInterface $before): int;
}
