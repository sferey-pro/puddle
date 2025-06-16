<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use App\Shared\Domain\Event\DomainEvent;

final readonly class RecurringCostCreated extends DomainEvent
{
    public function __construct(
        private RecurringCostId $recurringCostId,
    ) {
        parent::__construct($this->recurringCostId);
    }

    public static function eventName(): string
    {
        return 'cost_management.recurringcost.created';
    }

    public function recurringCostId(): RecurringCostId
    {
        return $this->recurringCostId;
    }
}
