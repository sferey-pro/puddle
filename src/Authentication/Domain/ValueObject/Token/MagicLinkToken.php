<?php

declare(strict_types=1);

namespace Authentication\Domain\ValueObject\Token;

/**
 * Token pour Magic Link (email)
 */
final class MagicLinkToken extends AbstractToken
{
    private const TOKEN_LENGTH = 64;
    public const EXPIRY_MINUTES = 15;

    public static function create(string $token, \DateTimeImmutable $expiresAt): self
    {
        return new self(
            $token,
            $expiresAt,
        );
    }

    public static function fromString(string $value, ?\DateTimeImmutable $expiresAt = null): self
    {
        return new self(
            $value,
            $expiresAt ?? new \DateTimeImmutable('+' . self::EXPIRY_MINUTES . ' minutes')
        );
    }

    public function type(): string
    {
        return 'magic_link';
    }

    protected function validate(): void
    {
        if (strlen($this->value) !== self::TOKEN_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('Magic link token must be %d characters long', self::TOKEN_LENGTH)
            );
        }

        if (!ctype_xdigit($this->value)) {
            throw new \InvalidArgumentException('Magic link token must be hexadecimal');
        }
    }

    /**
     * Génère l'URL complète du magic link
     */
    public function toUrl(string $baseUrl): string
    {
        return sprintf('%s/auth/magic-link/%s', rtrim($baseUrl, '/'), $this->value);
    }

}
