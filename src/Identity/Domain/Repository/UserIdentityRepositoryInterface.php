<?php

declare(strict_types=1);

namespace Identity\Domain\Repository;

use Identity\Domain\UserIdentity;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Repository\RepositoryInterface;
use Kernel\Domain\Repository\SpecificationRepositoryInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface UserIdentityRepositoryInterface extends RepositoryInterface, SpecificationRepositoryInterface
{
    /**
     * Sauvegarde un agrégat UserIdentity.
     */
    public function save(UserIdentity $userIdentity): void;

    /**
     * Trouve un UserIdentity par l'ID du compte.
     */
    public function ofId(UserId $userId): ?UserIdentity;

    /**
     * Vérifie si un identifiant est déjà utilisé.
     */
    public function existsByIdentity(Identifier $identifier): bool;

    /**
     * Trouve un UserIdentity par un de ses identifiants.
     */
    public function ofIdentifier(Identifier $identifier): ?UserIdentity;

    /**
     * Supprime un UserIdentity.
     */
    public function remove(UserIdentity $userIdentity): void;

    /**
     * Compte les UserIdentity qui ont un identifiant spécifique.
     * Utilisée pour l'implémentation des specifications.
     */
    public function countByIdentifier(Identifier $identifier): int;
}
