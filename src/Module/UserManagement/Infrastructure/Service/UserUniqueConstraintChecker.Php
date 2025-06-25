<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Service;

use App\Core\Application\Validator\UniqueConstraintCheckerInterface;
use App\Module\UserManagement\Domain\Repository\ProfileRepositoryInterface;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * @package App\Module\UserManagement\Infrastructure\Service
 *
 * Implémentation concrète du vérificateur de contraintes d'unicité pour le module UserManagement.
 * Il assure que les règles d'unicité (comme l'email ou le nom affiché) sont respectées
 * en interrogeant directement la source de vérité du domaine (WriteModel via Doctrine ORM).
 */
final readonly class UserUniqueConstraintChecker implements UniqueConstraintCheckerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ProfileRepositoryInterface $profileRepository,
    ) {
    }

    /**
     * @throws \InvalidArgumentException Si un contexte d'entité ou un type de contrainte non reconnu est fourni,
     * signalant une mauvaise utilisation du service pour ce module.
     */
    public function isUnique(string $entityContext, string $constraintType, mixed $value, ?string $excludeId = null): bool
    {
        // Seuls les contrôles d'unicité liés aux utilisateurs sont gérés par ce checker.
        if ('user' !== $entityContext) {
            throw new \InvalidArgumentException(\sprintf('Le contexte d\'entité "%s" n\'est pas géré par ce vérificateur d\'unicité.', $entityContext));
        }

        // Convertit l'ID d'exclusion en objet valeur UserId si présent.
        $excludeUserId = null;
        if (null !== $excludeId) {
            $excludeUserId = UserId::fromString($excludeId);
        }

        // Délègue la vérification au dépôt approprié en fonction du type de contrainte.
        return match ($constraintType) {
            'email' => $this->isEmailUnique($value, $excludeUserId),
            'displayName' => $this->isDisplayNameUnique($value, $excludeUserId),
            default => throw new \InvalidArgumentException(\sprintf('Le type de contrainte "%s" n\'est pas reconnu pour la vérification d\'unicité des utilisateurs.', $constraintType)),
        };
    }

    /**
     * Vérifie si l'adresse email est unique dans le système, en excluant potentiellement un utilisateur donné.
     *
     * @param string $email L'adresse email à vérifier.
     * @param UserId|null $excludeId L'ID de l'utilisateur à ignorer dans la vérification (pour les mises à jour).
     * @return bool Vrai si l'email est unique, faux sinon.
     */
    private function isEmailUnique(string $email, ?UserId $excludeId): bool
    {
        return !$this->userRepository->existsUserWithEmail(new Email($email), $excludeId);
    }

    /**
     * Vérifie si le nom d'utilisateur (username) est unique dans le système, en excluant potentiellement un utilisateur donné.
     *
     * @param string $username Le nom d'affichage à vérifier.
     * @param UserId|null $excludeId L'ID de l'utilisateur à ignorer dans la vérification (pour les mises à jour de profil).
     * @return bool Vrai si le nom d'affichage est unique, faux sinon.
     */
    private function isDisplayNameUnique(string $username, ?UserId $excludeId): bool
    {
        return !$this->profileRepository->existsProfileWithUsername($username, $excludeId);
    }
}
