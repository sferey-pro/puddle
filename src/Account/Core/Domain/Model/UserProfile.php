<?php

declare(strict_types=1);

namespace Account\Core\Domain\Model;

use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Profil utilisateur contenant les informations personnelles et préférences.
 *
 * Cette entité est séparée d'Account pour permettre une évolution
 * indépendante des données de profil vs données d'authentification.
 */
final class UserProfile
{
    private UserId $id;
    private string $displayName;
    private ?string $firstName;
    private ?string $lastName;
    private ?string $avatarUrl;
    private string $locale;
    private string $timezone;
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    private function __construct(
        UserId $id,
        string $displayName,
        ?string $firstName,
        ?string $lastName,
        ?string $avatarUrl,
        string $locale,
        string $timezone,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->setDisplayName($displayName);
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->avatarUrl = $avatarUrl;
        $this->setLocale($locale);
        $this->setTimezone($timezone);
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Crée un nouveau profil utilisateur avec les valeurs par défaut.
     */
    public static function create(
        UserId $userId,
        string $displayName,
        string $locale = 'en_US',
        string $timezone = 'UTC'
    ): self {
        $now = new \DateTimeImmutable();

        return new self(
            id: $userId,
            displayName: $displayName,
            firstName: null,
            lastName: null,
            avatarUrl: null,
            locale: $locale,
            timezone: $timezone,
            createdAt: $now,
            updatedAt: $now
        );
    }

    /**
     * Crée un profil complet.
     */
    public static function createComplete(
        UserId $userId,
        string $displayName,
        string $firstName,
        string $lastName,
        string $locale = 'en_US',
        string $timezone = 'UTC'
    ): self {
        $profile = self::create($userId, $displayName, $locale, $timezone);
        $profile->updatePersonalInfo($firstName, $lastName);

        return $profile;
    }

    /**
     * Met à jour les informations personnelles.
     */
    public function updatePersonalInfo(string $firstName, string $lastName): void
    {
        $this->firstName = trim($firstName);
        $this->lastName = trim($lastName);
        $this->recordUpdate();
    }

    /**
     * Change le nom d'affichage.
     */
    public function changeDisplayName(string $displayName): void
    {
        $this->setDisplayName($displayName);
        $this->recordUpdate();
    }

    /**
     * Met à jour l'avatar.
     */
    public function updateAvatar(string $avatarUrl): void
    {
        if (!filter_var($avatarUrl, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException('Invalid avatar URL');
        }

        $this->avatarUrl = $avatarUrl;
        $this->recordUpdate();
    }

    /**
     * Supprime l'avatar.
     */
    public function removeAvatar(): void
    {
        $this->avatarUrl = null;
        $this->recordUpdate();
    }

    /**
     * Change les préférences régionales.
     */
    public function updateLocalePreferences(string $locale, string $timezone): void
    {
        $this->setLocale($locale);
        $this->setTimezone($timezone);
        $this->recordUpdate();
    }

    /**
     * Génère le nom complet de l'utilisateur.
     */
    public function getFullName(): string
    {
        if ($this->firstName && $this->lastName) {
            return sprintf('%s %s', $this->firstName, $this->lastName);
        }

        return $this->displayName;
    }

    /**
     * Vérifie si le profil est complet.
     */
    public function isComplete(): bool
    {
        return $this->firstName !== null
            && $this->lastName !== null
            && $this->avatarUrl !== null;
    }

    /**
     * Retourne les initiales pour un avatar par défaut.
     */
    public function getInitials(): string
    {
        if ($this->firstName && $this->lastName) {
            return strtoupper($this->firstName[0] . $this->lastName[0]);
        }

        $parts = explode(' ', $this->displayName);
        if (count($parts) >= 2) {
            return strtoupper($parts[0][0] . $parts[1][0]);
        }

        return strtoupper(substr($this->displayName, 0, 2));
    }

    // ========== MÉTHODES PRIVÉES ==========

    private function setDisplayName(string $displayName): void
    {
        $displayName = trim($displayName);

        if (strlen($displayName) < 3) {
            throw new \InvalidArgumentException('Display name must be at least 3 characters long');
        }

        if (strlen($displayName) > 100) {
            throw new \InvalidArgumentException('Display name cannot exceed 100 characters');
        }

        $this->displayName = $displayName;
    }

    private function setLocale(string $locale): void
    {
        // Validation basique du format locale (ex: en_US, fr_FR)
        if (!preg_match('/^[a-z]{2}_[A-Z]{2}$/', $locale)) {
            throw new \InvalidArgumentException('Invalid locale format');
        }

        $this->locale = $locale;
    }

    private function setTimezone(string $timezone): void
    {
        // Vérifier que le timezone est valide
        if (!in_array($timezone, timezone_identifiers_list(), true)) {
            throw new \InvalidArgumentException('Invalid timezone');
        }

        $this->timezone = $timezone;
    }

    private function recordUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ========== GETTERS ==========

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
