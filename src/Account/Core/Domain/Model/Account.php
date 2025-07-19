<?php

declare(strict_types=1);

namespace Account\Core\Domain\Model;

use Account\Core\Domain\Event\AccountCreated;
use Account\Core\Domain\Event\AccountActivated;
use Account\Core\Domain\Event\AccountDeleted;
use Account\Core\Domain\Event\AccountReactivated;
use Account\Core\Domain\Event\AccountRestored;
use Account\Lifecycle\Domain\Model\AccountState;
use Account\Lifecycle\Domain\Model\AccountStateData;
use Account\Lifecycle\Domain\Model\State\ActiveState;
use Account\Lifecycle\Domain\Model\State\PendingState;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Clock\SystemTime;
use Kernel\Domain\Aggregate\AggregateRoot;
use Kernel\Domain\Contract\Entity\Timestampable;
use Kernel\Domain\Contract\Entity\Blameable;
use Kernel\Domain\Contract\Entity\Versionable;
use Kernel\Domain\Contract\Entity\SoftDeletable;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class Account extends AggregateRoot implements
    Timestampable,
    Blameable,
    Versionable,
    SoftDeletable
{
    // ==================== PROPRIÉTÉS PRINCIPALES ====================

    /**
     * État persisté via Doctrine (classe concrète)
     */
    private AccountStateData $stateData;

    /**
     * Cache de l'objet State comportemental
     */
    private ?AccountState $_state = null;

    private(set) Identifier $identifier;
    private(set) \DateTimeImmutable $verifiedAt;

    // ==================== PROPRIÉTÉS POUR CONTRACTS ====================

    // Timestampable
    private \DateTimeImmutable $createdAt;
    private \DateTimeImmutable $updatedAt;

    // Blameable
    private ?UserId $createdBy = null;
    private ?UserId $updatedBy = null;

    // Versionable
    private int $version = 0;

    // SoftDeletable
    private ?\DateTimeImmutable $deletedAt = null;

    private function __construct(
        private(set) UserId $id,
        Identifier $identifier,
    ) {
        $this->identifier = $identifier;
        $this->stateData = AccountStateData::fromState(new PendingState());

        $this->raise(new AccountCreated($id, $identifier));
    }

    public static function create(UserId $userId, Identifier $identifier): self
    {
        return new self($userId, $identifier);
    }

    // ==================== MÉTHODES MÉTIER ====================

    public function verify(): void
    {
        $this->getState()->verify($this);
        $this->raise(new AccountActivated($this->id));
    }

    public function reactivate(): void
    {
        $this->getState()->reactivate($this);
        $this->raise(new AccountReactivated($this->id));
    }

    public function markAsVerified(): void {
        $this->verifiedAt = SystemTime::now();
    }

    // ==================== IMPLÉMENTATION TIMESTAMPABLE ====================

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    // ==================== IMPLÉMENTATION BLAMEABLE ====================

    public function getCreatedBy(): ?UserId
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?UserId
    {
        return $this->updatedBy;
    }

    public function setCreatedBy(UserId $userId): void
    {
        $this->createdBy = $userId;
    }

    public function setUpdatedBy(UserId $userId): void
    {
        $this->updatedBy = $userId;
    }

    // ==================== IMPLÉMENTATION VERSIONABLE ====================

    public function getVersion(): int
    {
        return $this->version;
    }

    public function incrementVersion(): void
    {
        $this->version++;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }

    // ==================== IMPLÉMENTATION SOFTDELETABLE ====================

    public function delete(): void
    {
        $this->getState()->delete($this);
        $this->deletedAt = SystemTime::now();

        $this->raise(new AccountDeleted($this->id));
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

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    // ==================== ÉTAT : INTERFACE MÉTIER ====================

    /**
     * Retourne l'objet State comportemental (interface).
     * Utilise un cache pour éviter la reconstruction répétée.
     */
    public function getState(): AccountState
    {
        if (null === $this->_state) {
            $this->_state = $this->stateData->toState();
        }

        return $this->_state;
    }

    public function changeState(AccountState $newState): void
    {
        // Mise à jour des données persistées
        $this->stateData = AccountStateData::fromState($newState);

        // Invalidation du cache
        $this->_state = null;
    }

    // ==================== QUERY SUR L'ÉTAT ====================

    public function canBeVerified(): bool
    {
        return $this->getState()->canBeVerified($this);
    }

    public function canBeSuspended(): bool
    {
        return $this->getState()->canBeSuspended($this);
    }

    public function canBeReactivated(): bool
    {
        return $this->getState()->canBeReactivated($this);
    }

    public function canBeDeleted(): bool
    {
        return $this->getState()->canBeDeleted($this);
    }

}
