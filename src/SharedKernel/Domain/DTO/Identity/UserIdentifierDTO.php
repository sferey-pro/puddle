<?php

declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Identity;

/**
 * DTO représentant un identifiant unique d'un utilisateur
 * Fait partie de l'ACL pour exposer les identifiants aux autres contextes
 */
final readonly class UserIdentifierDTO
{
    public function __construct(
        public string $type,              // email, username, phone, oauth_google, etc.
        public string $value,             // john@example.com, johndoe, +1234567890
        public bool $isPrimary,
        public bool $isVerified,
        public \DateTimeImmutable $attachedAt,
        public ?\DateTimeImmutable $verifiedAt = null
    ) {}

    /**
     * Helper pour vérifier si c'est un email
     */
    public function isEmail(): bool
    {
        return $this->type === 'email';
    }

    /**
     * Helper pour vérifier si c'est un username
     */
    public function isUsername(): bool
    {
        return $this->type === 'username';
    }

    /**
     * Helper pour vérifier si c'est un identifiant OAuth
     */
    public function isOAuth(): bool
    {
        return str_starts_with($this->type, 'oauth_');
    }

    /**
     * Retourne un label user-friendly pour le type
     */
    public function getTypeLabel(): string
    {
        return match($this->type) {
            'email' => 'Email',
            'username' => 'Username',
            'phone' => 'Phone Number',
            'oauth_google' => 'Google Account',
            'oauth_github' => 'GitHub Account',
            'oauth_facebook' => 'Facebook Account',
            default => ucfirst(str_replace('_', ' ', $this->type))
        };
    }

    /**
     * Retourne une version masquée de la valeur pour l'affichage
     */
    public function getMaskedValue(): string
    {
        return match($this->type) {
            'email' => $this->maskEmail($this->value),
            'phone' => $this->maskPhone($this->value),
            default => $this->value
        };
    }

    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return $email;
        }

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            return $name . '***@' . $domain;
        }

        return substr($name, 0, 2) . '***' . substr($name, -1) . '@' . $domain;
    }

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) < 6) {
            return $phone;
        }

        return substr($phone, 0, 3) . '***' . substr($phone, -2);
    }
}
