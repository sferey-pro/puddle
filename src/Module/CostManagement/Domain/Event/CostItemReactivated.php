<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\Enum\CostItemStatus;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

/**
 * Événement émis lorsqu'un poste de coût archivé est réactivé.
 * Nouveau Status possible Active ou Couvert.
 */
final readonly class CostItemReactivated extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private CostItemStatus $newStatus = CostItemStatus::ACTIVE,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.reactivated';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function newStatus(): CostItemStatus
    {
        return $this->newStatus;
    }
}
