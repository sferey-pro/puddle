<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface as AuthUserRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Repository\ProfileRepositoryInterface;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface as UserManagementUserRepositoryInterface;
use App\Shared\Domain\Service\UniqueConstraintCheckerInterface;

/**
 * Implémentation concrète et globale du vérificateur de contraintes d'unicité.
 * Ce service centralise la logique de vérification pour diverses entités et contraintes
 * à travers l'application, en interrogeant les dépôts du WriteModel de chaque module concerné.
 */
final readonly class GlobalUniqueConstraintChecker implements UniqueConstraintCheckerInterface
{
    public function __construct(
        private UserManagementUserRepositoryInterface $userManagementUserRepository,
        private ProfileRepositoryInterface $profileRepository,
        private AuthUserRepositoryInterface $authUserRepository,
    ) {
    }

    /**
     * Vérifie si une valeur est unique pour un contexte d'entité et un type de contrainte spécifiques.
     *
     * @param string      $entityContext  le contexte ou le type d'entité (ex: 'user', 'user_account', 'product')
     * @param string      $constraintType le type de contrainte à vérifier (ex: 'email', 'displayName', 'name')
     * @param mixed       $value          la valeur à vérifier pour son unicité
     * @param string|null $excludeId      un identifiant d'entité à exclure de la vérification (pour les mises à jour)
     *
     * @throws \InvalidArgumentException si le contexte d'entité ou le type de contrainte n'est pas géré
     *
     * @return bool vrai si la valeur est unique, faux sinon
     */
    public function isUnique(string $entityContext, string $constraintType, mixed $value, ?string $excludeId = null): bool
    {
        // La conversion de $excludeId doit être spécifique au contexte d'entité
        $this->validateValueType($value, $constraintType);

        return match ($entityContext) {
            'user' => $this->checkUserContext($constraintType, $value, $excludeId),
            'user_account' => $this->checkUserAccountContext($constraintType, $value, $excludeId),
            default => throw new \InvalidArgumentException(\sprintf('Le contexte d\'entité "%s" est inconnu pour la vérification d\'unicité.', $entityContext)),
        };
    }

    /**
     * Vérifie l'unicité dans le contexte 'user'.
     *
     * @throws \InvalidArgumentException
     */
    private function checkUserContext(string $constraintType, mixed $value, ?string $excludeId): bool
    {
        $excludeUserId = null !== $excludeId ? UserId::fromString($excludeId) : null;

        return match ($constraintType) {
            'email' => !$this->userManagementUserRepository->existsUserWithEmail(new Email($value), $excludeUserId),
            'displayName' => !$this->profileRepository->existsProfileWithUsername($value, $excludeUserId),
            default => throw new \InvalidArgumentException(\sprintf('Le type de contrainte "%s" est inconnu pour le contexte "user".', $constraintType)),
        };
    }

    /**
     * Vérifie l'unicité dans le contexte 'user_account'.
     *
     * @throws \InvalidArgumentException
     */
    private function checkUserAccountContext(string $constraintType, mixed $value, ?string $excludeId): bool
    {
        $excludeUserId = null !== $excludeId ? UserId::fromString($excludeId) : null;

        return match ($constraintType) {
            'email' => !$this->authUserRepository->existsUserWithEmail(new Email($value), $excludeUserId),
            default => throw new \InvalidArgumentException(\sprintf('Le type de contrainte "%s" est inconnu pour le contexte "user_account".', $constraintType)),
        };
    }

    /**
     * Assure que le type de la valeur passée pour la vérification est une chaîne de caractères.
     *
     * @throws \InvalidArgumentException si la valeur n'est pas une chaîne ou un objet convertible en chaîne
     */
    private function validateValueType(mixed $value, string $constraintType): void
    {
        if (!\is_string($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new \InvalidArgumentException(\sprintf('La valeur pour la contrainte "%s" doit être une chaîne ou un objet convertible en chaîne, "%s" donné.', $constraintType, get_debug_type($value)));
        }
    }
}
