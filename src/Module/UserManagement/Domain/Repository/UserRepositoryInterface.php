<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Repository;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\User;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * Définit le contrat pour l'accès aux données des utilisateurs (agrégat User).
 * Il s'agit d'un "Port" dans l'architecture hexagonale, permettant au domaine
 * de persister et de récupérer les utilisateurs sans connaître les détails d'implémentation.
 */
interface UserRepositoryInterface extends RepositoryInterface
{
    /**
     * Sauvegarde un agrégat User, garantissant sa persistance.
     */
    public function save(User $user): void;

    /**
     * Recherche un utilisateur par son ID.
     */
    public function ofId(UserId $id): ?User;

    /**
     * Recherche un utilisateur par son adresse email.
     */
    public function ofEmail(Email $email): ?User;

    /**
     * Vérifie l'existence d'un utilisateur avec une adresse email donnée.
     * Permet d'exclure un utilisateur spécifique de la vérification (utile lors des mises à jour).
     *
     * @param Email       $email     L'adresse email à vérifier
     * @param UserId|null $excludeId L'identifiant de l'utilisateur à ignorer
     *
     * @return bool vrai si un utilisateur avec cet email existe (hors exclusion), faux sinon
     */
    public function existsUserWithEmail(Email $email, ?UserId $excludeId = null): bool;
}
