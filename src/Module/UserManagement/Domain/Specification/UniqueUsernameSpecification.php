<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Specification;

use App\Core\Application\Validator\UniqueConstraintCheckerInterface;
use App\Core\Specification\AbstractSpecification;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\ValueObject\Username;

final class UniqueUsernameSpecification extends AbstractSpecification
{
    public function __construct(
        private readonly UniqueConstraintCheckerInterface $uniqueConstraintChecker,
    ) {
    }

    /**
     * Vérifie si le nom d'utilisateur donné satisfait la règle d'unicité métier.
     *
     * @param mixed       $username  L'objet valeur Username ou DisplayName à vérifier
     * @param UserId|null $excludeId un identifiant utilisateur à exclure de la vérification,
     *                               nécessaire lors de la modification du profil d'un utilisateur existant
     *
     * @throws \InvalidArgumentException si l'entrée n'est pas un objet valeur attendu
     *
     * @return bool vrai si le nom est unique, faux sinon
     */
    public function isSatisfiedBy(mixed $username, ?UserId $excludeId = null): bool
    {
        if (!$username instanceof Username) {
            throw new \InvalidArgumentException('Attendu un objet valeur Username.');
        }

        return $this->uniqueConstraintChecker->isUnique('user', 'displayName', $username->value, $excludeId?->value);
    }
}
