<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Port\In;

/**
 * Port principal exposé par le contexte Authentication pour les opérations d'authentification.
 *
 * @context-boundary
 * Direction: IN (External Contexts → Authentication)
 * Type: Port (Interface)
 * Protocol: Sync via Direct Method Call
 *
 * Ce port constitue l'API publique du contexte Authentication. Il est utilisé
 * par les autres contextes pour gérer l'authentification des utilisateurs.
 *
 * @see \Authentication\Infrastructure\Adapter\In\AuthenticationServiceAdapter
 */
interface AuthenticationServicePort
{
    /**
     * Crée des credentials de type Magic Link pour un nouvel utilisateur.
     *
     * Called by: Account Context
     * When: Pendant le processus d'inscription (Registration Saga)
     *
     * @param string $userId ID de l'utilisateur nouvellement créé
     * @param string $email Email qui servira d'identifiant de connexion
     * @param array<string, mixed> $metadata Métadonnées additionnelles (IP, User-Agent, etc.)
     *
     * @throws CredentialCreationException Si la création échoue
     */
    public function createMagicLinkCredentials(
        string $userId,
        string $email,
        array $metadata = []
    ): void;

    /**
     * Crée des credentials de type OTP (SMS) pour un utilisateur.
     *
     * Called by: Account Context
     * When: Activation de l'authentification 2FA ou inscription par téléphone
     *
     * @param string $userId ID de l'utilisateur
     * @param string $phoneNumber Numéro de téléphone pour l'envoi du code
     * @param array<string, mixed> $metadata Métadonnées additionnelles
     *
     * @throws CredentialCreationException Si la création échoue
     */
    public function createOTPCredentials(
        string $userId,
        string $phoneNumber,
        array $metadata = []
    ): void;

    /**
     * Vérifie un token d'authentification (Magic Link ou OTP).
     *
     * Called by: API Gateway / Frontend Controllers
     * When: Tentative de connexion de l'utilisateur
     *
     * @param string $identifier Email ou téléphone
     * @param string $token Token à vérifier (hash pour magic link, code pour OTP)
     * @return array{userId: string, isValid: bool, metadata: array}
     *
     * @throws InvalidTokenException Si le token est invalide ou expiré
     */
    public function verifyAuthenticationToken(
        string $identifier,
        string $token
    ): array;

    /**
     * Révoque tous les credentials actifs d'un utilisateur.
     *
     * Called by: Account Context
     * When: Suppression de compte, suspension, ou demande utilisateur
     *
     * @param string $userId ID de l'utilisateur
     * @param string $reason Raison de la révocation
     */
    public function revokeAllUserCredentials(
        string $userId,
        string $reason
    ): void;
}
