<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Port\Out;

/**
 * Port définissant les besoins du contexte Authentication envers le contexte Identity.
 *
 * @context-boundary
 * Direction: OUT (Authentication → Identity)
 * Type: Port (Interface)
 * Protocol: Sync via ACL Interface
 *
 * Ce port permet à Authentication de résoudre et valider les identifiants
 * utilisateurs via le contexte Identity.
 *
 * @see \Authentication\Infrastructure\Adapter\Out\IdentityContextAdapter
 */
interface IdentityContextPort
{
    /**
     * Résout un identifiant (email/phone) vers un userId.
     *
     * Calls to: Identity Context
     * Used in: Login process, quand l'utilisateur fournit un identifiant
     *
     * @param string $identifier Email, téléphone ou autre identifiant
     * @return string|null UserId ou null si non trouvé
     *
     * @throws IdentityContextException Si la communication échoue
     */
    public function resolveUserIdFromIdentifier(string $identifier): ?string;

    /**
     * Vérifie si un identifiant appartient à un utilisateur.
     *
     * Calls to: Identity Context
     * Used in: Validation avant génération de token
     *
     * @param string $userId ID de l'utilisateur
     * @param string $identifier Identifiant à vérifier
     * @return bool True si l'identifiant appartient à l'utilisateur
     */
    public function userOwnsIdentifier(string $userId, string $identifier): bool;

    /**
     * Récupère tous les identifiants d'un utilisateur.
     *
     * Calls to: Identity Context
     * Used in: Listing des méthodes de connexion disponibles
     *
     * @param string $userId ID de l'utilisateur
     * @return array<array{type: string, value: string, verified: bool}>
     */
    public function getUserIdentifiers(string $userId): array;

    /**
     * Détermine le type d'un identifiant.
     *
     * Calls to: Identity Context
     * Used in: Choix de la méthode d'authentification (email vs SMS)
     *
     * @param string $identifier Identifiant à analyser
     * @return string Type ('email', 'phone', 'unknown')
     */
    public function getIdentifierType(string $identifier): string;
}
