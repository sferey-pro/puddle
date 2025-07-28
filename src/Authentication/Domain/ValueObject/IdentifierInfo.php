<?php

declare(strict_types=1);

namespace Authentication\Domain\ValueObject;

/**
 * Value Object représentant les informations d'un identifier
 *
 * Version simplifiée d'un Identifier pour le contexte Authentication
 */
final readonly class IdentifierInfo
{
    public function __construct(
        private string $type,
        private string $value,
        private bool $isPrimary,
        private bool $isVerified
    ) {
        if (empty($type)) {
            throw new \InvalidArgumentException('Le type d\'identifier ne peut pas être vide');
        }

        if (empty($value)) {
            throw new \InvalidArgumentException('La valeur de l\'identifier ne peut pas être vide');
        }
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * Vérifie si c'est un email
     */
    public function isEmail(): bool
    {
        return $this->type === 'email';
    }

    /**
     * Vérifie si c'est un téléphone
     */
    public function isPhone(): bool
    {
        return $this->type === 'phone';
    }

    /**
     * Retourne une représentation masquée pour l'affichage
     */
    public function masked(): string
    {
        return match($this->type) {
            'email' => $this->maskEmail(),
            'phone' => $this->maskPhone(),
            default => $this->maskDefault()
        };
    }

    private function maskEmail(): string
    {
        $parts = explode('@', $this->value);
        if (count($parts) !== 2) {
            return $this->maskDefault();
        }

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 3) {
            return str_repeat('*', strlen($name)) . '@' . $domain;
        }

        return substr($name, 0, 2) . str_repeat('*', strlen($name) - 3) . substr($name, -1) . '@' . $domain;
    }

    private function maskPhone(): string
    {
        $cleaned = preg_replace('/\D/', '', $this->value);

        if (strlen($cleaned) < 6) {
            return str_repeat('*', strlen($cleaned));
        }

        return substr($cleaned, 0, 3) . str_repeat('*', strlen($cleaned) - 6) . substr($cleaned, -3);
    }

    private function maskDefault(): string
    {
        if (strlen($this->value) <= 4) {
            return str_repeat('*', strlen($this->value));
        }

        return substr($this->value, 0, 2) . str_repeat('*', strlen($this->value) - 4) . substr($this->value, -2);
    }

    /**
     * Compare avec un autre IdentifierInfo
     */
    public function equals(IdentifierInfo $other): bool
    {
        return $this->type === $other->type && $this->value === $other->value;
    }

    /**
     * Convertit en array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'is_primary' => $this->isPrimary,
            'is_verified' => $this->isVerified,
        ];
    }

    /**
     * Factory depuis un array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            type: $data['type'],
            value: $data['value'],
            isPrimary: $data['is_primary'] ?? false,
            isVerified: $data['is_verified'] ?? false
        );
    }
}
