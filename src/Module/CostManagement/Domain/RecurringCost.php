<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain;

use App\Module\CostManagement\Domain\Enum\RecurringCostStatus;
use App\Module\CostManagement\Domain\Event\RecurringCostCreated;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\Domain\ValueObject\RecurrenceRule;
use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Model\DomainEventTrait;
use App\Shared\Domain\Service\ClockInterface;

/**
 * Représente la planification d'un coût récurrent.
 * Cet agrégat simple contient la règle de récurrence et une référence
 * vers le CostItem qui sert de modèle.
 */
class RecurringCost extends AggregateRoot
{
    use DomainEventTrait;

    private \DateTimeImmutable $updatedAt;
    private ?\DateTimeImmutable $lastGeneratedAt = null;

    private function __construct(
        private RecurringCostId $id,
        private CostItemId $templateCostItemId, // Référence vers le modèle
        private RecurrenceRule $recurrenceRule,
        private RecurringCostStatus $status,
        private \DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * Crée une nouvelle planification de coût récurrent.
     *
     * @param CostItemId     $templateCostItemId L'ID du CostItem servant de modèle
     * @param RecurrenceRule $recurrenceRule     la règle de périodicité
     */
    public static function create(
        CostItemId $templateCostItemId,
        RecurrenceRule $recurrenceRule,
        ClockInterface $clock,
    ): self {
        $id = RecurringCostId::generate();

        $recurringCost = new self(
            $id,
            $templateCostItemId,
            $recurrenceRule,
            RecurringCostStatus::ACTIVE,
            $clock->now()
        );

        $recurringCost->updatedAt = $clock->now();
        $recurringCost->recordDomainEvent(new RecurringCostCreated($id));

        return $recurringCost;
    }

    public function markAsGenerated(ClockInterface $clock): void
    {
        $this->lastGeneratedAt = $clock->now();
        $this->updatedAt = $clock->now();
    }

    public function id(): RecurringCostId
    {
        return $this->id;
    }

    public function templateCostItemId(): CostItemId
    {
        return $this->templateCostItemId;
    }

    public function recurrenceRule(): RecurrenceRule
    {
        return $this->recurrenceRule;
    }

    public function status(): RecurringCostStatus
    {
        return $this->status;
    }

    public function lastGeneratedAt(): ?\DateTimeImmutable
    {
        return $this->lastGeneratedAt;
    }
}
