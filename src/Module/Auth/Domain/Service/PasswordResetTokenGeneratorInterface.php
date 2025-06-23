<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Service;

use App\Module\Auth\Domain\ValueObject\HashedToken;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Port pour le service de génération de tokens sécurisés.
 *
 * Cette interface définit le contrat pour un service technique capable de créer
 * des tokens cryptographiquement sûrs. Elle abstrait la complexité de l'algorithme
 * (HMAC, Sélecteur/Vérificateur) de la couche Application qui l'utilise.
 */
interface PasswordResetTokenGeneratorInterface
{
    /**
     * Crée un jeu de tokens complet pour une nouvelle demande.
     *
     * @return array{selector: string, hashedToken: HashedToken, publicToken: string} le jeu de tokens
     */
    public function generate(UserId $userId, \DateTimeImmutable $expiresAt): array;

    /**
     * Recrée la signature de sécurité (le "hashedToken") pour un token donné.
     * Cette méthode est essentielle pour la phase de vérification, afin de comparer
     * de manière sécurisée le token de l'utilisateur avec ce qui est attendu.
     */
    public function createHashedToken(UserId $userId, \DateTimeImmutable $expiresAt, string $verifier): HashedToken;
}
