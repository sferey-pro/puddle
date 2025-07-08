<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Repository;

use App\Core\Domain\Repository\RepositoryInterface;
use App\Core\Domain\Repository\SpecificationRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\User;

/**
 * Définit le contrat pour l'accès aux données des utilisateurs (agrégat User).
 * Il s'agit d'un "Port" dans l'architecture hexagonale, permettant au domaine
 * de persister et de récupérer les utilisateurs sans connaître les détails d'implémentation.
 */
interface UserRepositoryInterface extends RepositoryInterface, SpecificationRepositoryInterface
{
    public function add(User $user): void;

    public function remove(User $model): void;

    public function ofId(UserId $id): ?User;

    public function ofEmail(EmailAddress $email): ?User;

    /**
     * Vérifie l'existence d'un utilisateur avec une adresse email donnée.
     * Permet d'exclure un utilisateur spécifique de la vérification (utile lors des mises à jour).
     *
     * @param Email       $email     L'adresse email à vérifier
     * @param UserId|null $excludeId L'identifiant de l'utilisateur à ignorer
     *
     * @return bool vrai si un utilisateur avec cet email existe (hors exclusion), faux sinon
     */
    public function existsUserWithEmail(EmailAddress $email, ?UserId $excludeId = null): bool;
}
