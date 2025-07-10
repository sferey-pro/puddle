<?php

declare(strict_types=1);

namespace Identity\Domain\Event;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Event\DomainEvent;

/**
 * Événement émis lorsqu'un identifiant est détaché d'un compte.
 */
final readonly class IdentityDetachedFromAccount extends DomainEvent
{
    public function __construct(
        private(set) UserId $userId,
        private(set) Identifier $detachedIdentifier,
    ) {
        parent::__construct($userId);
    }

    public static function eventName(): string
    {
        return 'identity.identity.detached_from_account';
    }
}
