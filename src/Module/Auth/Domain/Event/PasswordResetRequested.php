<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Événement publié lorsqu'un utilisateur a demandé une réinitialisation de mot de passe.
 */
final readonly class PasswordResetRequested extends DomainEvent
{
    public function __construct(
        private PasswordResetRequestId $aggregateId,
        private UserId $userId,
        private Email $email,
        private \DateTimeImmutable $expiresAt,
        private string $plainToken,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.password_reset.requested';
    }

    public function passwordResetRequestId(): PasswordResetRequestId
    {
        return $this->aggregateId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function expiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function plainToken(): string
    {
        return $this->plainToken;
    }
}
