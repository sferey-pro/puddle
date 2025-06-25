<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Specification;

use App\Core\Specification\AbstractSpecification;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/** *
 * Spécification métier pour vérifier l'unicité de l'adresse email lors de l'enregistrement ou la mise à jour d'un compte Auth.
 * Elle incarne la règle métier : "Un email de compte utilisateur doit être unique pour l'authentification."
 * Elle utilise le service générique de vérification de contraintes.
 */
final class UniqueEmailSpecification extends AbstractSpecification
{
    public function __construct(
        private readonly UniqueConstraintCheckerInterface $uniqueConstraintChecker,
    ) {
    }

    /**
     * Vérifie si l'adresse email donnée est unique dans le contexte des comptes utilisateurs.
     *
     * @param mixed       $email     L'objet valeur Email à vérifier
     * @param UserId|null $excludeId un identifiant utilisateur à exclure de la vérification
     *
     * @throws \InvalidArgumentException si l'email fourni n'est pas un objet valeur Email
     *
     * @return bool vrai si l'email est unique, faux sinon
     */
    public function isSatisfiedBy(mixed $email, ?UserId $excludeId = null): bool
    {
        if (!$email instanceof Email) {
            throw new \InvalidArgumentException('Attendu un objet valeur Email.');
        }

        // Utilise le contexte 'user_account' pour ce checker
        return $this->uniqueConstraintChecker->isUnique('user_account', 'email', $email->value, $excludeId?->value);
    }
}
