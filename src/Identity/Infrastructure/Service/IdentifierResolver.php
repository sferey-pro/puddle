<?php

namespace Identity\Infrastructure\Service;

use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Identity\Domain\Service\IdentifierResolverInterface;
use Kernel\Domain\Result;

final class IdentifierResolver implements IdentifierResolverInterface
{
    public function resolve(string $value): Result
    {
        $value = trim($value);

        if (empty($value)) {
            return Result::failure(
                new \InvalidArgumentException('Identifier cannot be empty')
            );
        }

        // Détecter le type
        $type = $this->detectType($value);

        $identifier = match($type) {
            'email' => EmailIdentity::create($value),
            'phone' => PhoneIdentity::create($value),
            default => throw new \InvalidArgumentException('Unknown identifier type')
        };

        return $identifier;
    }

    public function detectType(string $value): ?string
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        if ($this->looksLikePhoneNumber($value)) {
            return 'phone';
        }

        // Extensible pour d'autres types
        // if (preg_match('/^LC\d{9}$/i', $value)) {
        //     return 'loyalty_card';
        // }

        return null;
    }

    private function looksLikePhoneNumber(string $value): bool
    {
        $cleaned = preg_replace('/[^0-9+]/', '', $value);
        return (str_starts_with($cleaned, '+') || strlen($cleaned) >= 10);
    }
}
