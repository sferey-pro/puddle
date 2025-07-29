<?php

declare(strict_types=1);

namespace Authentication\Domain\ValueObject\Token;

/**
 * Token OTP pour SMS
 */
final class OTPCode extends AbstractToken
{
    private const CODE_LENGTH = 6;
    private const EXPIRY_MINUTES = 5;

    public static function generate(int $length = self::CODE_LENGTH, $duration = self::EXPIRY_MINUTES): self
    {
        // Génère un code à 6 chiffres
        $code = str_pad((string) random_int(0, 999999), $length, '0', STR_PAD_LEFT);

        return new self(
            $code,
            new \DateTimeImmutable('+' . $duration . ' minutes')
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
        return 'otp';
    }

    protected function validate(): void
    {
        if (strlen($this->value) !== self::CODE_LENGTH) {
            throw new \InvalidArgumentException(
                sprintf('OTP must be %d digits long', self::CODE_LENGTH)
            );
        }

        if (!ctype_digit($this->value)) {
            throw new \InvalidArgumentException('OTP must contain only digits');
        }
    }

    /**
     * Format pour affichage utilisateur (avec espaces)
     */
    public function toDisplayFormat(): string
    {
        // Transforme "123456" en "123 456"
        return chunk_split($this->value, 3, ' ');
    }
}
