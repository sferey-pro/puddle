<?php

namespace Account\Core\Domain;

use Account\Core\Domain\Event\AccountActivated;
use Account\Core\Domain\Event\AccountCreated;
use Account\Core\Domain\Event\AccountDeleted;
use Account\Core\Domain\Event\AccountLocked;
use Account\Core\Domain\Event\AccountReactivated;
use Account\Core\Domain\Event\AccountRestored;
use Account\Core\Domain\Event\AccountSuspended;
use Account\Lifecycle\Domain\AccountStatus;
use Account\Lifecycle\Domain\State\AccountState;
use Account\Lifecycle\Domain\State\ActiveState;
use Account\Lifecycle\Domain\State\PendingState;
use Identity\Domain\UserIdentity;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Clock\SystemTime;
use Kernel\Domain\Aggregate\AggregateRoot;
use Kernel\Domain\Contract\Entity\SoftDeletable;
use Kernel\Domain\Contract\Entity\Timestampable;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class Account extends AggregateRoot implements
    Timestampable
{

    private(set) AccountState $state;
    private(set) EmailAddress $email;

    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $deletedAt = null;
    private ?\DateTimeImmutable $verifiedAt = null;

    private function __construct(
        private(set) UserId $id,
        private(set) Identifier $identifier,
    ) {
        $this->state = new PendingState();
        $this->createdAt = SystemTime::now();
        $this->updatedAt = SystemTime::now();

        $this->raise(new AccountCreated($id, $identifier));
    }

    public static function create(UserId $userId, Identifier $identifier): self
    {
        return new self($userId, $identifier);
    }

    public function verify(): void
    {
        $this->state->verify($this);
        $this->raise(new AccountActivated($this->id));
    }

    public function suspend(string $reason, ?\DateTimeImmutable $until = null): void
    {
        $this->state->suspend($this, $reason, $until);
        $this->raise(new AccountSuspended($this->id, $reason));
    }

    public function reactivate(): void
    {
        $this->state->reactivate($this);
        $this->raise(new AccountReactivated($this->id));
    }

    public function lock(string $reason): void
    {
        $this->state->lock($this, $reason);
        $this->raise(new AccountLocked($this->id, $reason));
    }

    public function restore(): void
    {
        if (!$this->isDeleted()) {
            throw new \DomainException('Account is not deleted');
        }

        $this->deletedAt = null;
        $this->changeState(new ActiveState());
        $this->raise(new AccountRestored($this->id));
    }

    public function delete(): void
    {
        $this->state->delete($this);
        $this->raise(new AccountDeleted($this->id));
    }

    public function changeState(AccountState $newState): void
    {
        $this->state = $newState;
        $this->updatedAt = SystemTime::now();
    }

    /**
     * Queries sur l'état.
     */
    public function canBeVerified(): bool
    {
        return $this->state->canBeVerified($this);
    }

    public function canBeSuspended(): bool
    {
        return $this->state->canBeSuspended($this);
    }

    public function canBeReactivated(): bool
    {
        return $this->state->canBeReactivated($this);
    }

    public function canBeDeleted(): bool
    {
        return $this->state->canBeDeleted($this);
    }

    public function isDeleted(): bool {
        return $this->deletedAt !== null;
    }

    public function markAsDeleted(): void {
        $this->deletedAt = SystemTime::now();
    }

    public function markAsVerified(): void {
        $this->verifiedAt = SystemTime::now();
    }

    // Getters
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
    public function getDeletedAt(): ?\DateTimeImmutable { return $this->deletedAt; }
    public function getVerifiedAt(): ?\DateTimeImmutable { return $this->verifiedAt; }

}
