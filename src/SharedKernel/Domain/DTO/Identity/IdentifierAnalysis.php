<?php

declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Identity;

/**
 * Résultat immutable de l'analyse d'un identifiant.
 *
 * PHILOSOPHIE : Ce DTO contient TOUTES les métadonnées dont les autres
 * contextes peuvent avoir besoin, évitant ainsi des appels multiples.
 */
final readonly class IdentifierAnalysis
{
    private function __construct(
        public bool $isValid,
        public ?string $type,              // 'email', 'phone', null si invalide
        public ?string $rawValue,          // Valeur originale saisie
        public ?string $normalizedValue,   // Valeur nettoyée/normalisée
        public ?string $maskedValue,       // Valeur masquée pour affichage
        public ?string $channel,           // Canal de notification : 'email', 'sms'
        public ?string $displayMessage,    // Message à afficher à l'utilisateur
        public ?string $errorMessage,      // Message d'erreur si invalide
        public array $metadata = []        // Données additionnelles si besoin
    ) {}

    /**
     * Factory pour un identifiant email valide.
     */
    public static function validEmail(
        string $rawValue,
        string $normalizedValue,
        string $maskedValue
    ): self {
        return new self(
            isValid: true,
            type: 'email',
            rawValue: $rawValue,
            normalizedValue: $normalizedValue,
            maskedValue: $maskedValue,
            channel: 'email',
            displayMessage: 'We will send a magic link to your email address.',
            errorMessage: null,
            metadata: ['domain' => explode('@', $normalizedValue)[1] ?? null]
        );
    }

    /**
     * Factory pour un identifiant téléphone valide.
     */
    public static function validPhone(
        string $rawValue,
        string $normalizedValue,
        string $maskedValue,
        ?string $countryCode = null
    ): self {
        return new self(
            isValid: true,
            type: 'phone',
            rawValue: $rawValue,
            normalizedValue: $normalizedValue,
            maskedValue: $maskedValue,
            channel: 'sms',
            displayMessage: 'We will send a verification code to your phone.',
            errorMessage: null,
            metadata: ['country_code' => $countryCode]
        );
    }

    /**
     * Factory pour un identifiant invalide.
     */
    public static function invalid(string $rawValue, string $errorMessage): self
    {
        return new self(
            isValid: false,
            type: null,
            rawValue: $rawValue,
            normalizedValue: null,
            maskedValue: null,
            channel: null,
            displayMessage: null,
            errorMessage: $errorMessage
        );
    }

    /**
     * Helpers pour les consommateurs.
     */
    public function isEmail(): bool
    {
        return $this->type === 'email';
    }

    public function isPhone(): bool
    {
        return $this->type === 'phone';
    }

    public function requiresOTP(): bool
    {
        return $this->channel === 'sms';
    }

    public function requiresMagicLink(): bool
    {
        return $this->channel === 'email';
    }

    /**
     * Pour les logs/debug.
     */
    public function toArray(): array
    {
        return [
            'is_valid' => $this->isValid,
            'type' => $this->type,
            'raw_value' => $this->rawValue,
            'normalized_value' => $this->normalizedValue,
            'masked_value' => $this->maskedValue,
            'channel' => $this->channel,
            'display_message' => $this->displayMessage,
            'error_message' => $this->errorMessage,
            'metadata' => $this->metadata,
        ];
    }
}
