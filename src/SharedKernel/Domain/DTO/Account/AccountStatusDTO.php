<?php

declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Account;

use Account\Lifecycle\Domain\Model\State\AccountState;
use Account\Lifecycle\Domain\Model\State\ActiveState;
use Account\Lifecycle\Domain\Model\State\SuspendedState;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class AccountStatusDTO
{
    public function __construct(
        public UserId $id,
        public AccountState $state,
        public \DateTimeImmutable $createdAt,
        public ?string $suspensionReason = null,
        public ?\DateTimeImmutable $suspendedUntil = null,
        public ?\DateTimeImmutable $lastActivityAt = null
    ) {}

    public function isActive(): bool
    {
        return $this->state instanceof ActiveState;
    }

    public function isSuspended(): bool
    {
        return $this->state instanceof SuspendedState &&
               ($this->suspendedUntil === null || $this->suspendedUntil > new \DateTimeImmutable());
    }

    public function canAuthenticate(): bool
    {
        return $this->isActive();
    }
}
