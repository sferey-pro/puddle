<?php

declare(strict_types=1);

namespace Identity\Domain\Event;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Event\DomainEvent;

/**
 * Événement émis lorsqu'un agrégat UserIdentity est créé pour la première fois.
 * Cela se produit généralement lors de l'inscription d'un nouvel utilisateur.
 */
final readonly class IdentityCreated extends DomainEvent
{
    /**
     * @param UserId $userId L'ID du compte pour lequel l'identité a été créée.
     * @param Identifier $identifier Le premier identifiant qui a été attaché.
     */
    public function __construct(
        private(set) UserId $userId,
        private(set) Identifier $identifier
    ) {
        parent::__construct($userId);
    }

    /**
     * {@inheritDoc}
     */
    public static function eventName(): string
    {
        return 'identity.identity.created';
    }
}
