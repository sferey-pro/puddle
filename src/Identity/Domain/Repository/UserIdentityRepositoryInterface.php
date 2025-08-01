<?php

declare(strict_types=1);

namespace Identity\Domain\Repository;

use Identity\Domain\Model\UserIdentity;
use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository pour l'agrégat UserIdentity.
 *
 * PHILOSOPHIE :
 * - Focus sur les opérations CRUD et recherches basiques
 */
interface UserIdentityRepositoryInterface
{
    // ==================== CRUD BASIQUE ====================

    /**
     * Persiste un UserIdentity (create ou update).
     */
    public function save(UserIdentity $userIdentity): void;

    /**
     * Supprime un UserIdentity.
     */
    public function remove(UserIdentity $userIdentity): void;

    // ==================== RECHERCHES ESSENTIELLES ====================

    /**
     * Vérifie si un identifier existe déjà.
     * Cas d'usage : Validation d'unicité lors de l'inscription
     *
     * @param string $type Le type d'identifier
     * @param string $value La valeur de l'identifier
     */
    public function existsByIdentity(Identifier $identifier): bool;

    /**
     * Trouve un UserIdentity par valeur d'identifier (email, phone, etc.).
     * Cas d'usage : Login, vérification d'unicité
     *
     * @param string $value La valeur de l'identifier (ex: "user@example.com")
     */
    public function ofIdentifier(Identifier $identifier): ?UserIdentity;

    /**
     * Récupère uniquement le UserId pour un identifier donné.
     * Cas d'usage : Authentication rapide sans charger tout l'agrégat
     *
     * @param string $value La valeur de l'identifier
     */
    public function findUserIdByIdentifier(Identifier $identifier): ?UserId;


    /**
     * Trouve un UserIdentity par son UserId.
     * Cas d'usage : Chargement depuis un Command/Query avec UserId connu
     */
    public function findByUserId(UserId $userId): ?UserIdentity;
}
