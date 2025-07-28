<?php

declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Identity;

use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * DTO représentant l'ensemble des identifiants d'un utilisateur
 * Fait partie de l'ACL pour exposer l'identité complète aux autres contextes
 */
final readonly class UserIdentifiersDTO
{
    /**
     * @param UserIdentifierDTO[] $identifiers
     */
    public function __construct(
        public UserId $userId,
        public array $identifiers,
        public ?UserIdentifierDTO $primaryIdentifier
    ) {}

    /**
     * Retourne le nombre total d'identifiants
     */
    public function count(): int
    {
        return count($this->identifiers);
    }

    /**
     * Trouve un identifiant par type
     */
    public function findByType(string $type): ?UserIdentifierDTO
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier->type === $type) {
                return $identifier;
            }
        }
        return null;
    }

    /**
     * Retourne tous les identifiants d'un type donné
     */
    public function findAllByType(string $type): array
    {
        return array_filter(
            $this->identifiers,
            fn(UserIdentifierDTO $identifier) => $identifier->type === $type
        );
    }

    /**
     * Retourne l'email principal (ou le premier email trouvé)
     */
    public function getPrimaryEmail(): ?string
    {
        // D'abord chercher l'email primaire
        if ($this->primaryIdentifier?->isEmail()) {
            return $this->primaryIdentifier->value;
        }

        // Sinon chercher n'importe quel email
        $emailIdentifier = $this->findByType('email');
        return $emailIdentifier?->value;
    }

    /**
     * Retourne le username s'il existe
     */
    public function getUsername(): ?string
    {
        $usernameIdentifier = $this->findByType('username');
        return $usernameIdentifier?->value;
    }

    /**
     * Vérifie si l'utilisateur a au moins un identifiant vérifié
     */
    public function hasVerifiedIdentifier(): bool
    {
        foreach ($this->identifiers as $identifier) {
            if ($identifier->isVerified) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retourne tous les identifiants vérifiés
     */
    public function getVerifiedIdentifiers(): array
    {
        return array_filter(
            $this->identifiers,
            fn(UserIdentifierDTO $identifier) => $identifier->isVerified
        );
    }

    /**
     * Retourne tous les identifiants non vérifiés
     */
    public function getUnverifiedIdentifiers(): array
    {
        return array_filter(
            $this->identifiers,
            fn(UserIdentifierDTO $identifier) => !$identifier->isVerified
        );
    }

    /**
     * Vérifie si l'utilisateur a un type d'identifiant spécifique
     */
    public function hasIdentifierType(string $type): bool
    {
        return $this->findByType($type) !== null;
    }

    /**
     * Retourne un array simple pour la sérialisation
     */
    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'identifiers' => array_map(
                fn(UserIdentifierDTO $identifier) => [
                    'type' => $identifier->type,
                    'value' => $identifier->value,
                    'isPrimary' => $identifier->isPrimary,
                    'isVerified' => $identifier->isVerified,
                    'attachedAt' => $identifier->attachedAt->format('Y-m-d H:i:s'),
                    'verifiedAt' => $identifier->verifiedAt?->format('Y-m-d H:i:s')
                ],
                $this->identifiers
            ),
            'primaryIdentifier' => $this->primaryIdentifier ? [
                'type' => $this->primaryIdentifier->type,
                'value' => $this->primaryIdentifier->value
            ] : null
        ];
    }
}
