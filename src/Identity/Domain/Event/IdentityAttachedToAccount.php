<?php

declare(strict_types=1);

namespace Identity\Domain\Event;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Event\DomainEvent;

/**
 * Événement émis lorsqu'un nouvel identifiant est attaché à un agrégat UserIdentity existant.
 */
final readonly class IdentityAttachedToAccount extends DomainEvent
{
    /**
     * @param UserId $userId L'ID du compte concerné.
     * @param Identifier $attachedIdentifier Le nouvel identifiant qui vient d'être attaché.
     */
    public function __construct(
        private(set) UserId $userId,
        private(set) Identifier $attachedIdentifier,
    ) {
        parent::__construct($userId);
    }

    /**
     * {@inheritDoc}
     */
    public static function eventName(): string
    {
        return 'identity.identity.attached_to_account';
    }
}
