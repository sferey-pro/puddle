<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Core\Domain\Repository\RepositoryInterface;
use App\Core\Domain\Repository\SpecificationRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\SharedContext\Domain\ValueObject\Username;

/**
 * Définit le contrat pour la persistance et la récupération des comptes utilisateurs (agrégat UserAccount)
 * pour le contexte d'authentification.
 */
interface UserAccountRepositoryInterface extends RepositoryInterface, SpecificationRepositoryInterface
{
    public function add(UserAccount $model): void;

    public function remove(UserAccount $model): void;

    public function ofEmail(Email $email): ?UserAccount;

    public function ofUsername(Username $email): ?UserAccount;

    public function ofId(UserId $id): ?UserAccount;

    /**
     * Vérifie si un compte utilisateur avec l'adresse email donnée existe déjà.
     * Cette méthode est cruciale pour garantir l'unicité des emails lors de l'enregistrement.
     *
     * @param Email       $email     L'adresse email à vérifier
     * @param UserId|null $excludeId un identifiant utilisateur à exclure de la vérification,
     *                               utile lors de la mise à jour d'un email existant
     *
     * @return bool vrai si un compte existe avec cet email (hors exclusion), faux sinon
     */
    public function existsUserWithEmail(Email $email, ?UserId $excludeId = null): bool;
}
