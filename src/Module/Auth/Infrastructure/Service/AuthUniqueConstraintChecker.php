<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Service;

use App\Core\Application\Validator\UniqueConstraintCheckerInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Implémentation concrète du vérificateur de contraintes d'unicité pour le module Auth.
 * Il est responsable de vérifier l'unicité des données critiques (comme l'email)
 * spécifiques au domaine de l'authentification.
 */
final readonly class AuthUniqueConstraintChecker implements UniqueConstraintCheckerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws \InvalidArgumentException si un contexte d'entité ou un type de contrainte non reconnu est fourni,
     *                                   indiquant une tentative de vérifier une contrainte hors du scope de ce checker
     */
    public function isUnique(string $entityContext, string $constraintType, mixed $value, ?string $excludeId = null): bool
    {
        // Ce checker est spécifiquement pour les contraintes liées aux comptes utilisateurs du module Auth.
        if ('user_account' !== $entityContext) { // Utilisation d'un contexte plus précis pour Auth
            throw new \InvalidArgumentException(\sprintf('Le contexte d\'entité "%s" n\'est pas géré par ce vérificateur d\'unicité d\'authentification.', $entityContext));
        }

        // Convertit l'ID d'exclusion en objet valeur UserId si présent.
        $excludeUserId = null;
        if (null !== $excludeId) {
            $excludeUserId = UserId::fromString($excludeId);
        }

        // Délègue la vérification en fonction du type de contrainte.
        return match ($constraintType) {
            'email' => $this->isEmailUnique($value, $excludeUserId),
            default => throw new \InvalidArgumentException(\sprintf('Le type de contrainte "%s" n\'est pas reconnu pour la vérification d\'unicité des comptes utilisateurs.', $constraintType)),
        };
    }

    /**
     * Vérifie si l'adresse email est unique pour un compte utilisateur.
     *
     * @param string      $email     L'adresse email à vérifier
     * @param UserId|null $excludeId L'ID du compte utilisateur à ignorer (pour les mises à jour)
     *
     * @return bool vrai si l'email est unique pour un compte utilisateur, faux sinon
     */
    private function isEmailUnique(string $email, ?UserId $excludeId): bool
    {
        return !$this->userRepository->existsUserWithEmail(new Email($email), $excludeId);
    }
}
