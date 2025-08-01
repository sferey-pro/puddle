<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Port\In;

/**
 * Port pour la gestion avancée des credentials.
 *
 * @context-boundary
 * Direction: IN (Admin/Support Tools → Authentication)
 * Type: Port (Interface)
 * Protocol: Sync via Direct Method Call
 *
 * Ce port expose des opérations de maintenance et de support sur les credentials.
 * Il est principalement utilisé par les outils d'administration.
 *
 * @see \Authentication\Infrastructure\Adapter\In\CredentialManagementAdapter
 */
interface CredentialManagementPort
{
    /**
     * Liste tous les credentials actifs d'un utilisateur.
     *
     * Called by: Admin Dashboard, Support Tools
     * When: Investigation de problèmes de connexion
     *
     * @param string $userId ID de l'utilisateur
     * @return array<array{type: string, identifier: string, createdAt: string, expiresAt: string}>
     */
    public function listUserCredentials(string $userId): array;

    /**
     * Invalide un credential spécifique.
     *
     * Called by: Security Tools
     * When: Détection d'activité suspecte sur un credential
     *
     * @param string $credentialId ID du credential
     * @param string $reason Raison de l'invalidation
     */
    public function invalidateCredential(
        string $credentialId,
        string $reason
    ): void;

    /**
     * Récupère les statistiques de connexion d'un utilisateur.
     *
     * Called by: Analytics Service, User Dashboard
     * When: Affichage des statistiques de sécurité
     *
     * @param string $userId ID de l'utilisateur
     * @param \DateTimeInterface $since Depuis quand calculer
     * @return array{attempts: int, successes: int, failures: int, lastLogin: ?string}
     */
    public function getUserLoginStatistics(
        string $userId,
        \DateTimeInterface $since
    ): array;
}
