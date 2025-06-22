<?php

namespace App\Module\Auth\Domain\Service;

use App\Module\Auth\Domain\ValueObject\HashedToken;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Port pour la génération de tokens de réinitialisation de mot de passe.
 * Définit le contrat pour l'infrastructure qui implémentera la logique de génération.
 */
interface PasswordResetTokenGeneratorInterface
{
    /**
     * Génère un nouveau token et retourne sa version hashée pour le stockage,
     * ainsi que le token en clair pour l'envoi à l'utilisateur.
     *
     * @return array{selector: string, hashedToken: HashedToken, publicToken: string}
     */
    public function generate(UserId $userId, \DateTimeImmutable $expiresAt): array;

    /**
     * Crée un token hashé à partir des composants pour la comparaison.
     */
    public function createHashedToken(UserId $userId, \DateTimeImmutable $expiresAt, string $verifier): HashedToken;
}
