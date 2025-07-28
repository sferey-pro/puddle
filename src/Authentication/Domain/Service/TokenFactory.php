<?php

declare(strict_types=1);

namespace Authentication\Domain\Service;

use Authentication\Domain\ValueObject\Token;
use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Authentication\Domain\ValueObject\Token\OtpCode;

final class TokenFactory
{
    public function createToken(string $type): Token
    {
        return match ($type) {
            'magic_link' => MagicLinkToken::generate(),
            'otp' => OtpCode::generate(),
            default => throw new \InvalidArgumentException("Unknown token type: {$type}")
        };
    }
    
    public function recreateToken(string $type, string $value, \DateTimeImmutable $expiresAt): Token
    {
        return match ($type) {
            'magic_link' => MagicLinkToken::fromString($value, $expiresAt),
            'otp' => OtpCode::fromString($value, $expiresAt),
            default => throw new \InvalidArgumentException("Unknown token type: {$type}")
        };
    }
}