<?php

declare(strict_types=1);

namespace Authentication\Domain\ValueObject;

use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Value Object représentant la collection d'identifiers d'un utilisateur
 *
 * Utilisé par l'ACL pour transmettre les identifiers depuis Identity vers Authentication
 */
final class UserIdentifiers
{
    /**
     * @var array<string, IdentifierInfo>
     */
    private array $identifiers = [];

    private ?IdentifierInfo $primary = null;

    /**
     * @param array<array{type: string, value: string, isPrimary: bool, isVerified: bool}> $identifiersData
     */
    public function __construct(
        private(set) readonly UserId $userId,
        array $identifiersData
    ) {
        foreach ($identifiersData as $data) {
            $identifier = new IdentifierInfo(
                $data['type'],
                $data['value'],
                $data['isPrimary'],
                $data['isVerified']
            );

            $this->identifiers[$data['type']] = $identifier;

            if ($data['isPrimary']) {
                $this->primary = $identifier;
            }
        }
    }

    /**
     * Factory pour un utilisateur sans identifiers
     */
    public static function empty(UserId $userId): self
    {
        return new self($userId, []);
    }

    /**
     * Retourne tous les identifiers
     * @return array<string, IdentifierInfo>
     */
    public function all(): array
    {
        return $this->identifiers;
    }

    /**
     * Retourne l'identifier principal
     */
    public function primary(): ?IdentifierInfo
    {
        return $this->primary;
    }

    /**
     * Vérifie si un type d'identifier existe
     */
    public function has(string $type): bool
    {
        return isset($this->identifiers[$type]);
    }

    /**
     * Retourne un identifier par type
     */
    public function get(string $type): ?IdentifierInfo
    {
        return $this->identifiers[$type] ?? null;
    }

    /**
     * Retourne l'email si disponible
     */
    public function email(): ?string
    {
        return $this->identifiers['email']?->value();
    }

    /**
     * Retourne le téléphone si disponible
     */
    public function phone(): ?string
    {
        return $this->identifiers['phone']?->value();
    }

    /**
     * Vérifie si au moins un identifier est vérifié
     */
    public function hasVerifiedIdentifier(): bool
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier->isVerified()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retourne les identifiers vérifiés
     * @return array<string, IdentifierInfo>
     */
    public function verifiedIdentifiers(): array
    {
        return array_filter(
            $this->identifiers,
            fn(IdentifierInfo $identifier) => $identifier->isVerified()
        );
    }

    /**
     * Compte le nombre d'identifiers
     */
    public function count(): int
    {
        return count($this->identifiers);
    }

    /**
     * Vérifie si la collection est vide
     */
    public function isEmpty(): bool
    {
        return empty($this->identifiers);
    }

    /**
     * Retourne les types d'identifiers disponibles
     * @return string[]
     */
    public function availableTypes(): array
    {
        return array_keys($this->identifiers);
    }

    /**
     * Convertit en array pour serialisation
     */
    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'identifiers' => array_map(
                fn(IdentifierInfo $identifier) => $identifier->toArray(),
                $this->identifiers
            ),
            'primary' => $this->primary?->toArray(),
        ];
    }
}
