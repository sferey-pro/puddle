<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\LoginAttempts;

use App\Module\Auth\Domain\AuthenticationMethod;
use App\Module\Auth\Domain\LoginAttempts\Event\AuthenticationMethodLocked;
use App\Module\Auth\Domain\LoginAttempts\Event\LoginAttemptFailed;
use App\Module\Auth\Domain\LoginAttempts\Event\LoginAttemptsReset;
use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Core\Domain\ValueObject\Uid;

final class LoginAttempts extends AggregateRoot
{
    use DomainEventTrait;

    private const MAX_ATTEMPTS = 5;
    private const LOCK_DURATION = 'PT15M'; // ISO 8601 duration format

    private Uid $authenticationMethodId;

    private int $loginFailureAttempts = 0;
    private ?\DateTimeImmutable $lockedUntil = null;
    private ?\DateTimeImmutable $lastLoginFailureAt = null;

    private function __construct() {}

    public static function watch(AuthenticationMethod $method): self
    {
        $attempts = new self();
        // L'ID est polymorphique, il peut être un PasswordCredentialId, LoginLinkId, etc.
        $attempts->authenticationMethodId = $method->id;
        return $attempts;
    }

    public function id(): Uid
    {
        return $this->authenticationMethodId;
    }

    /**
     * Enregistre un échec de tentative de connexion.
     * @throws \DomainException Si la méthode est déjà bloquée.
     */
    public function recordFailure(\DateTimeImmutable $now): void
    {
        if ($this->isLocked($now)) {
            throw new \DomainException('Authentication method is currently locked.');
        }

        $this->failureCount++;

        $this->recordDomainEvent(new LoginAttemptFailed(
            $this->authenticationMethodId->toString(),
            $this->failureCount
        ));

        if ($this->failureCount >= self::MAX_ATTEMPTS) {
            $this->lock($now);
        }
    }

    /**
     * Réinitialise les tentatives après une connexion réussie.
     */
    public function recordSuccess(): void
    {
        if ($this->failureCount === 0) {
            return; // Pas de changement d'état nécessaire
        }

        $this->failureCount = 0;
        $this->lockedUntil = null;

        $this->recordDomainEvent(new LoginAttemptsReset($this->authenticationMethodId->toString()));
    }

    public function isLocked(\DateTimeImmutable $now): bool
    {
        return $this->lockedUntil !== null && $this->lockedUntil > $now;
    }

    private function lock(\DateTimeImmutable $now): void
    {
        $this->lockedUntil = $now->add(new \DateInterval(self::LOCK_DURATION));

        $this->recordDomainEvent(new AuthenticationMethodLocked(
            $this->authenticationMethodId->toString(),
            $this->lockedUntil
        ));
    }
}
