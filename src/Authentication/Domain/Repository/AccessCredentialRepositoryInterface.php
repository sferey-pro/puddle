<?php

declare(strict_types=1);

namespace Authentication\Domain\Repository;

use Authentication\Domain\Model\AccessCredential\AbstractAccessCredential;
use Authentication\Domain\ValueObject\Token;
use DateInterval;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository pour les credentials d'accès temporaires.
 *
 * PHILOSOPHIE : Focus sur l'authentification passwordless
 * - Magic Links pour email
 * - OTP codes pour SMS
 * - Gestion de l'expiration et usage unique
 */
interface AccessCredentialRepositoryInterface
{
    // ==================== CRUD BASIQUE ====================

    /**
     * Persiste un credential (MagicLink ou OTP).
     */
    public function save(AbstractAccessCredential $credential): void;

    /**
     * Supprime un credential.
     */
    public function remove(AbstractAccessCredential $credential): void;

    // ==================== RECHERCHES ESSENTIELLES ====================

    /**
     * Trouve le credential crée par un user pour un identifier.
     * Cas d'usage : Compensation de la création du credential
     */
    public function findByIdentifierAndUserId(Identifier $identifier, UserId $userId): ?AbstractAccessCredential;

    /**
     * Trouve un credential par son token.
     * Cas d'usage : Validation lors du login
     */
    public function findByToken(Token $token): ?AbstractAccessCredential;

    /**
     * Trouve les credentials actifs d'un utilisateur.
     * Cas d'usage : Vérifier qu'un seul credential actif par user
     *
     * @return AbstractAccessCredential[]
     */
    public function findActiveByUserId(UserId $userId): array;

    /**
     * Trouve le dernier credential créé pour un identifier.
     * Cas d'usage : Eviter le spam
     */
    public function findLatestByIdentifier(Identifier $identifier): ?AbstractAccessCredential;

    /**
     * Compte le nombre de tentatives récentes pour un identifiant donné.
     * Cas d'usage :  Pour le rate limiting.
     */
    public function countRecentAttempts(Identifier $identifier, \DateInterval $interval): int;


    // ==================== MAINTENANCE ====================

    /**
     * Supprime tous les credentials expirés.
     * Cas d'usage : Cron job de nettoyage
     *
     * @return int Nombre de credentials supprimés
     */
    public function removeExpired(): int;

    /**
     * Marque tous les credentials d'un utilisateur comme utilisés.
     * Cas d'usage : Sécurité après changement de mot de passe ou logout global
     */
    public function invalidateAllForUser(UserId $userId): void;


}
