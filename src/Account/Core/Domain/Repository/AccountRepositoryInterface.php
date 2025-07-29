<?php

declare(strict_types=1);

namespace Account\Core\Domain\Repository;

use Account\Core\Domain\Model\Account;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository pour l'agrégat Account.
 *
 * PHILOSOPHIE : KISS - Uniquement les opérations essentielles
 * - CRUD basique
 * - Recherche par identifiants pour l'authentification
 * - Pas de specifications complexes dans un premier temps
 */
interface AccountRepositoryInterface
{
    // ==================== CRUD BASIQUE ====================

    /**
     * Persiste un Account (create ou update).
     */
    public function save(Account $account): void;

    /**
     * Supprime un Account.
     */
    public function remove(Account $account): void;

    // ==================== RECHERCHES ESSENTIELLES ====================

    /**
     * Trouve un Account par son UserId.
     * Cas d'usage : Chargement depuis un Command/Query avec UserId connu
     */
    public function findById(UserId $userId): ?Account;

    /**
     * Trouve un Account par email.
     * Cas d'usage : Authentification, vérification d'unicité
     */
    public function findByEmail(string $email): ?Account;

    /**
     * Trouve un Account par numéro de téléphone.
     * Cas d'usage : Authentification par SMS, vérification d'unicité
     */
    public function findByPhone(string $phone): ?Account;

    // ==================== REQUÊTES OPTIMISÉES ====================

    /**
     * Vérifie si un Account existe.
     * Cas d'usage : Validations rapides sans charger l'agrégat
     */
    public function exists(UserId $userId): bool;

    /**
     * Vérifie si un email est déjà utilisé.
     * Cas d'usage : Validation lors de l'inscription
     */
    public function emailExists(string $email): bool;

    /**
     * Vérifie si un numéro de téléphone est déjà utilisé.
     * Cas d'usage : Validation lors de l'inscription
     */
    public function phoneExists(string $phone): bool;

    // ==================== REQUÊTES MÉTIER ====================

    /**
     * Compte les Accounts actifs.
     * Cas d'usage : Dashboard admin, métriques
     */
    public function countActive(): int;

    /**
     * Trouve les Accounts créés dans une période.
     * Cas d'usage : Reporting, analytics
     *
     * @return Account[]
     */
    public function findCreatedBetween(\DateTimeInterface $from, \DateTimeInterface $to): array;
}
