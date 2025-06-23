<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Service;

use App\Module\Auth\Domain\Service\PasswordResetTokenGeneratorInterface;
use App\Module\Auth\Domain\ValueObject\HashedToken;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Implémente le contrat de génération de token de manière sécurisée.
 *
 * En tant qu'adaptateur d'infrastructure, cette classe encapsule la complexité
 * cryptographique. Elle utilise le pattern "Sélecteur / Vérificateur" avec une
 * signature HMAC pour créer des tokens infalsifiables, garantissant que seuls
 * les tokens générés par le système avec la bonne clé secrète sont valides.
 */
final class SecureTokenGenerator implements PasswordResetTokenGeneratorInterface
{
    private const SELECTOR_LENGTH = 16;
    private const VERIFIER_LENGTH = 16;

    private readonly string $signingKey;

    /**
     * @param string $signingKey la clé secrète de l'application (ex: APP_SECRET), essentielle
     *                           pour créer des signatures HMAC uniques et sécurisées
     */
    public function __construct(
        #[Autowire('%env(APP_SECRET)%')]
        string $signingKey,
    ) {
        if (empty($signingKey)) {
            throw new \InvalidArgumentException('A non-empty APP_SECRET_KEY environment variable is required.');
        }

        $this->signingKey = $signingKey;
    }

    /**
     * Pour une demande de réinitialisation, génère un jeu complet de tokens :
     * - Un `selector` public pour retrouver la demande.
     * - Un `publicToken` (selector + verifier) à envoyer à l'utilisateur.
     * - Un `hashedToken` (la signature HMAC) à stocker en base de données comme preuve.
     */
    public function generate(UserId $userId, \DateTimeImmutable $expiresAt): array
    {
        $selector = bin2hex(random_bytes(self::SELECTOR_LENGTH));
        $verifier = bin2hex(random_bytes(self::VERIFIER_LENGTH));

        $publicToken = $selector.$verifier;

        $hashedToken = $this->createHashedToken($userId, $expiresAt, $verifier);

        return [
            'selector' => $selector,
            'hashedToken' => $hashedToken,
            'publicToken' => $publicToken,
        ];
    }

    /**
     * Recrée la signature HMAC à partir des informations d'une demande et du vérificateur
     * fourni par l'utilisateur. Le but est de la comparer à celle stockée en base de données
     * pour valider l'authenticité d'un token.
     */
    public function createHashedToken(UserId $userId, \DateTimeImmutable $expiresAt, string $verifier): HashedToken
    {
        $data = json_encode([$verifier, $userId->value, $expiresAt->getTimestamp()]);

        $signature = hash_hmac('sha256', $data, $this->signingKey, true);

        return new HashedToken(base64_encode($signature));
    }
}
