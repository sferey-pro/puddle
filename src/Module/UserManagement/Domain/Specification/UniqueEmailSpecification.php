<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Specification;

use App\Core\Application\Validator\UniqueConstraintCheckerInterface;
use App\Core\Specification\AbstractSpecification;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Spécification métier pour vérifier l'unicité de l'adresse email d'un utilisateur.
 * Elle incarne la règle métier : "Un email d'utilisateur doit être unique dans le système".
 * Elle s'appuie sur le service générique de vérification de contraintes pour l'implémentation technique.
 */
final class UniqueEmailSpecification extends AbstractSpecification
{
    public function __construct(
        private readonly UniqueConstraintCheckerInterface $uniqueConstraintChecker,
    ) {
    }

    /**
     * Vérifie si l'adresse email donnée satisfait la règle d'unicité métier.
     *
     * @param mixed       $email     L'objet valeur Email à vérifier
     * @param UserId|null $excludeId un identifiant utilisateur à exclure de la vérification,
     *                               nécessaire lors de la modification de l'email d'un utilisateur existant
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

        return $this->uniqueConstraintChecker->isUnique('user', 'email', $email->value, $excludeId?->value);
    }
}
