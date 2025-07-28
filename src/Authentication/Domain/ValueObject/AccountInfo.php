<?php

declare(strict_types=1);

namespace Authentication\Domain\ValueObject;

use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Value Object représentant les informations d'un compte pour l'authentification
 *
 * Utilisé par l'ACL pour transmettre les données Account vers Authentication
 * sans exposer l'entité Account complète
 */
final readonly class AccountInfo
{
    public function __construct(
        private(set) UserId $userId,
        private(set) ?EmailAddress $email,
        private(set) ?PhoneNumber $phone,
        private(set) bool $isActive,
        private(set) bool $isVerified,
        private(set) \DateTimeImmutable $createdAt
    ) {
        if ($email === null && $phone === null) {
            throw new \InvalidArgumentException('Au moins un moyen de contact (email ou téléphone) est requis');
        }
    }

    /**
     * Vérifie si le compte peut se connecter
     */
    public function canLogin(): bool
    {
        return $this->isActive && $this->isVerified;
    }

    /**
     * Retourne le contact principal (email en priorité)
     */
    public function primaryContact(): string
    {
        if ($this->email !== null) {
            return $this->email->value;
        }

        return $this->phone->value;
    }

    /**
     * Retourne le type de contact principal
     */
    public function primaryContactType(): string
    {
        return $this->email !== null ? 'email' : 'phone';
    }

    /**
     * Vérifie si le compte a un email
     */
    public function hasEmail(): bool
    {
        return $this->email !== null;
    }

    /**
     * Vérifie si le compte a un téléphone
     */
    public function hasPhone(): bool
    {
        return $this->phone !== null;
    }

    /**
     * Retourne l'âge du compte
     */
    public function accountAge(): \DateInterval
    {
        return $this->createdAt->diff(new \DateTimeImmutable());
    }

    /**
     * Factory method pour créer depuis un array (utile pour les tests)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            userId: UserId::fromString($data['user_id']),
            email: isset($data['email']) ? EmailAddress::create($data['email']) : null,
            phone: isset($data['phone']) ? PhoneNumber::create($data['phone']) : null,
            isActive: $data['is_active'] ?? true,
            isVerified: $data['is_verified'] ?? false,
            createdAt: new \DateTimeImmutable($data['created_at'] ?? 'now')
        );
    }

    /**
     * Convertit en array (pour serialisation)
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_active' => $this->isActive,
            'is_verified' => $this->isVerified,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s'),
        ];
    }
}
