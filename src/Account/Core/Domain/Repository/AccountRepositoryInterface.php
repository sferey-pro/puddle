<?php

declare(strict_types=1);

namespace Account\Core\Domain\Repository;

use Account\Core\Domain\Model\Account;
use Kernel\Domain\Repository\RepositoryInterface;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository pour l'agrégat Account.
 *
 * PHILOSOPHIE :
 * - CRUD
 * - Recherche par identifiants pour l'authentification
 *
 * @extends RepositoryInterface<Account, UserId>
 */
interface AccountRepositoryInterface extends RepositoryInterface
{
    // Recherche par critère unique
    // ============================

    /**
     * Trouve un Account par son UserId.
     */
    public function ofUserId(UserId $id): ?Account;

    /**
     * Trouve un Account par email.
     * Cas d'usage : Authentification, vérification d'unicité
     */
    public function ofEmail(EmailAddress $email): ?Account;

    /**
     * Trouve un Account par numéro de téléphone.
     * Cas d'usage : Authentification par SMS, vérification d'unicité
     */
    public function ofPhone(PhoneNumber $phone): ?Account;

    // Vérification existence
    // ======================

    /**
     * Vérifie si un Account existe.
     * Cas d'usage : Validations rapides sans charger l'agrégat
     */
    public function existsUserId(UserId $id): bool;

    /**
     * Vérifie si un email est déjà utilisé.
     * Cas d'usage : Validation lors de l'inscription
     */
    public function existsEmail(EmailAddress $email): bool;

    /**
     * Vérifie si un numéro de téléphone est déjà utilisé.
     * Cas d'usage : Validation lors de l'inscription
     */
    public function existsPhone(PhoneNumber $phone): bool;


    // Spécifique métier
    // =================

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
