<?php

namespace App\Module\Auth\Infrastructure\Service;

use App\Module\Auth\Domain\Service\PasswordResetTokenGeneratorInterface;
use App\Module\Auth\Domain\ValueObject\HashedToken;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Implémente la génération de token de manière sécurisée.
 * Cette classe est un adaptateur qui se branche sur le port défini dans le domaine.
 */
final class SecureTokenGenerator implements PasswordResetTokenGeneratorInterface
{
    private const SELECTOR_LENGTH = 16;
    private const VERIFIER_LENGTH = 16;

    private readonly string $signingKey;

    public function __construct(
        #[Autowire('%env(APP_SECRET)%')]
        string $signingKey
    ) {
        if (empty($signingKey)) {
            throw new \InvalidArgumentException('A non-empty APP_SECRET_KEY environment variable is required.');
        }

        $this->signingKey = $signingKey;
    }

    public function generate(UserId $userId, \DateTimeImmutable $expiresAt): array
    {
        // 1. On génère un sélecteur et un vérificateur aléatoires et sécurisés.
        $selector = bin2hex(random_bytes(self::SELECTOR_LENGTH));
        $verifier = bin2hex(random_bytes(self::VERIFIER_LENGTH));

        // 2. On crée le token public complet qui sera dans le lien de l'email.
        $publicToken = $selector . $verifier;

        // 3. On génère la signature HMAC sécurisée (notre "hashedToken").
        $hashedToken = $this->createHashedToken($userId, $expiresAt, $verifier);

        return [
            'selector' => $selector,
            'hashedToken' => $hashedToken,
            'publicToken' => $publicToken,
        ];
    }

    public function createHashedToken(UserId $userId, \DateTimeImmutable $expiresAt, string $verifier): HashedToken
    {
        $data = json_encode([$verifier, $userId->value, $expiresAt->getTimestamp()]);

        $signature = hash_hmac('sha256', $data, $this->signingKey, true);

        return new HashedToken(base64_encode($signature));
    }
}
