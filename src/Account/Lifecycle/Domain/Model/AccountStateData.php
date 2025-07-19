<?php

declare(strict_types=1);

namespace Account\Lifecycle\Domain\Model;

use Account\Lifecycle\Domain\Model\State\ActiveState;
use Account\Lifecycle\Domain\Model\State\DeletedState;
use Account\Lifecycle\Domain\Model\State\LockedState;
use Account\Lifecycle\Domain\Model\State\PendingState;
use Account\Lifecycle\Domain\Model\State\SuspendedState;

/**
 * Classe concrète pour la persistence Doctrine de l'état Account.
 *
 * Cette classe sert de wrapper pour sérialiser/désérialiser
 * les implémentations concrètes de l'interface AccountState.
 *
 * Architecture:
 * - AccountState (interface) : Comportements métier
 * - PendingState, ActiveState, etc. : Implémentations concrètes
 * - AccountStateData : Wrapper pour Doctrine
 */
final class AccountStateData
{
    public function __construct(
        private string $name,
        private ?string $reason = null,
        private ?array $metadata = null,
        private ?\DateTimeImmutable $changedAt = null,
        private ?\DateTimeImmutable $expiresAt = null,
        private bool $isActive = false,
        private int $priority = 0
    ) {
        $this->changedAt ??= new \DateTimeImmutable();
        $this->isActive = $this->name === 'active';
        $this->priority = $this->calculatePriority($this->name);
    }

    /**
     * Factory method pour créer depuis une instance AccountState.
     */
    public static function fromState(AccountState $state): self
    {
        $metadata = [];
        $expiresAt = null;
        $reason = null;

        // Extraction des données spécifiques selon le type d'état
        switch ($state::class) {
            case SuspendedState::class:
                /** @var SuspendedState $state */
                $reason = $state->reason;
                $expiresAt = $state->until;
                $metadata = [
                    'reason' => $state->reason,
                    'until' => $state->until?->format('c')
                ];
                break;

            case LockedState::class:
                /** @var LockedState $state */
                $reason = $state->reason;
                $metadata = [
                    'reason' => $state->reason,
                    'locked_at' => (new \DateTimeImmutable())->format('c')
                ];
                break;

            case DeletedState::class:
                $metadata = [
                    'deleted_at' => (new \DateTimeImmutable())->format('c')
                ];
                break;

            default:
                $metadata = [];
        }

        return new self(
            name: $state->getName(),
            reason: $reason,
            metadata: $metadata,
            changedAt: new \DateTimeImmutable(),
            expiresAt: $expiresAt
        );
    }

    /**
     * Reconstitue l'objet State concret depuis les données persistées.
     */
    public function toState(): AccountState
    {
        return match ($this->name) {
            'pending' => new PendingState(),
            'active' => new ActiveState(),
            'suspended' => new SuspendedState(
                reason: $this->reason ?? 'Unknown',
                until: $this->expiresAt
            ),
            'locked' => new LockedState(
                reason: $this->reason ?? 'Unknown'
            ),
            'deleted' => new DeletedState(),
            default => throw new \RuntimeException("Unknown state: {$this->name}")
        };
    }

    /**
     * Calcule la priorité de l'état pour les workflows.
     */
    private function calculatePriority(string $stateName): int
    {
        return match ($stateName) {
            'deleted' => 100,     // Plus restrictif
            'locked' => 80,
            'suspended' => 60,
            'pending' => 40,
            'active' => 20,       // Moins restrictif
            default => 0
        };
    }

    // ==================== GETTERS ====================

    public function getName(): string
    {
        return $this->name;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function getChangedAt(): ?\DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
