<?php

declare(strict_types=1);

namespace Authentication\Domain\Service;

/**
 * Port pour la génération de tokens sécurisés
 */
interface TokenGeneratorInterface
{
    public function generateMagicLinkToken(): string;
    public function generateOTPCode(): string;
    public function generateSecureToken(int $length = 32): string;
}
