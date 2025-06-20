<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Module\Auth\Domain\ValueObject\LoginLinkId;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement levé lorsqu'un nouvel utilisateur se connecte via son lien de connexion (MagicLink).
 */
final readonly class LoginLinkVerified extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private LoginLinkId $loginLinkId,
    ) {
        parent::__construct($this->aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.user.login_verified';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function loginLinkId(): LoginLinkId
    {
        return $this->loginLinkId;
    }
}
