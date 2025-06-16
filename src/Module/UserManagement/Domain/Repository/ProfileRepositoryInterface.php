<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Repository;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Profile;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * Définit le contrat pour l'accès aux données des profils utilisateurs (entité Profile).
 * Ce "Port" permet d'isoler le domaine des mécanismes de persistance des profils.
 */
interface ProfileRepositoryInterface extends RepositoryInterface
{
    /**
     * Sauvegarde une entité Profile, garantissant sa persistance.
     */
    public function save(Profile $profile): void;

    /**
     * Recherche un profil par l'identifiant de l'utilisateur auquel il est lié.
     */
    public function ofId(UserId $userId): ?Profile;

    /**
     * Vérifie l'existence d'un profil avec un nom d'utilisateur donné.
     * Permet d'exclure un utilisateur spécifique de la vérification (utile lors des mises à jour de profil).
     *
     * @param string      $username  le nom de l'utilisateur à vérifier
     * @param UserId|null $excludeId L'identifiant de l'utilisateur dont le profil est à ignorer
     *
     * @return bool vrai si un profil avec ce nom d'affichage existe (hors exclusion), faux sinon
     */
    public function existsProfileWithUsername(string $username, ?UserId $excludeId = null): bool;
}
