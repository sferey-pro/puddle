<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Service;

use Authentication\Domain\Service\TokenGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface as SymfonyTokenGenerator;

/**
 * Adapter utilisant le générateur de token Symfony
 */
final class SymfonyTokenGeneratorAdapter implements TokenGeneratorInterface
{
    public function __construct(
        private readonly SymfonyTokenGenerator $symfonyTokenGenerator
    ) {}

    public function generateMagicLinkToken(): string
    {
        // Utilise le générateur Symfony (URL-safe)
        return $this->symfonyTokenGenerator->generateToken();
    }

    public function generateOTPCode(): string
    {
        // OTP numérique 6 chiffres
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function generateSecureToken(int $length = 32): string
    {
        // Pour d'autres usages
        $token = $this->symfonyTokenGenerator->generateToken();

        if (strlen($token) > $length) {
            return substr($token, 0, $length);
        }

        return $token;
    }
}
