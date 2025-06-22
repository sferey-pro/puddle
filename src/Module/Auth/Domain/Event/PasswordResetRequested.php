<?php

namespace App\Module\Auth\Domain\Event;

use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;
use DateTimeImmutable;

/**
 * Événement publié lorsqu'un utilisateur a demandé une réinitialisation de mot de passe.
 */
final readonly class PasswordResetRequested extends DomainEvent
{
    public function __construct(
        PasswordResetRequestId $aggregateId,
        public readonly UserId $userId,
        public readonly Email $email,
        public readonly DateTimeImmutable $expiresAt,
        public string $plainToken,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.password_reset.requested';
    }
}
