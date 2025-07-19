<?php

declare(strict_types=1);

namespace Authentication\Domain\Specification;

use Kernel\Domain\Specification\SpecificationInterface;

/**
 * Valide qu'un identifiant est un email ou téléphone valide
 */
final class ValidIdentifierSpecification implements SpecificationInterface
{
    private ?string $reason = null;

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!is_string($candidate)) {
            $this->reason = 'Identifier must be a string';
            return false;
        }

        // Email valide ?
        if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
            return true;
        }

        // Téléphone valide ? (simplifié)
        $cleaned = preg_replace('/[^0-9+]/', '', $candidate);
        if (preg_match('/^\+?[1-9]\d{6,14}$/', $cleaned)) {
            return true;
        }

        $this->reason = 'Please provide a valid email address or phone number';
        return false;
    }

    public function failureReason(): ?string
    {
        return $this->reason;
    }
}
