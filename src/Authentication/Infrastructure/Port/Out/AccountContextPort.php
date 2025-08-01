<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Port\Out;

use Authentication\Infrastructure\Port\Out\DTO\AccountInfoDTO;

/**
 * Port définissant les besoins du contexte Authentication envers le contexte Account.
 *
 * @context-boundary
 * Direction: OUT (Authentication → Account)
 * Type: Port (Interface)
 * Protocol: Sync via ACL Interface
 *
 * Ce port représente les opérations dont Authentication a besoin du contexte
 * Account pour fonctionner correctement. C'est une dépendance externe requise.
 *
 * @see \Authentication\Infrastructure\Adapter\Out\AccountContextAdapter
 */
interface AccountContextPort
{
    /**
     * Récupère les informations complètes d'un compte.
     *
     * Calls to: Account Context
     * Used in: Vérification de l'état du compte, récupération des identifiants
     *
     * @param string $userId ID de l'utilisateur
     * @return AccountInfoDTO|null DTO des informations du compte ou null si non trouvé
     *
     * @throws AccountContextException Si la communication échoue
     */
    public function getAccountInfo(string $userId): ?AccountInfoDTO;

    /**
     * Vérifie si un compte utilisateur existe et est actif.
     *
     * Calls to: Account Context
     * Used in: Processus de login, avant de vérifier les credentials
     *
     * @param string $userId ID de l'utilisateur
     * @return bool True si le compte existe et est actif
     *
     * @throws AccountContextException Si la communication échoue
     */
    public function isAccountActive(string $userId): bool;

    /**
     * Récupère les informations de contact d'un compte.
     *
     * Calls to: Account Context
     * Used in: Génération de magic link, envoi d'OTP
     *
     * @param string $userId ID de l'utilisateur
     * @return array{email: ?string, phone: ?string, preferredChannel: string}
     *
     * @throws AccountNotFoundException Si le compte n'existe pas
     */
    public function getAccountContacts(string $userId): array;

    /**
     * Notifie le contexte Account d'une connexion réussie.
     *
     * Calls to: Account Context
     * Used in: Post-authentification, pour mise à jour lastLoginAt
     *
     * @param string $userId ID de l'utilisateur
     * @param array{ip: string, userAgent: string, timestamp: string} $metadata
     */
    public function notifySuccessfulLogin(string $userId, array $metadata): void;

    /**
     * Signale des tentatives de connexion suspectes.
     *
     * Calls to: Account Context
     * Used in: Après plusieurs échecs de connexion
     *
     * @param string $userId ID de l'utilisateur
     * @param int $failedAttempts Nombre de tentatives échouées
     * @param string $reason Raison du signalement
     */
    public function reportSuspiciousActivity(
        string $userId,
        int $failedAttempts,
        string $reason
    ): void;
}
