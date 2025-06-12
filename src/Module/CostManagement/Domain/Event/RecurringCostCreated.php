<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class RecurringCostCreated extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly RecurringCostId $recurringCostId,
    ) {
        parent::__construct();
    }

    public function recurringCostId(): RecurringCostId
    {
        return $this->recurringCostId;
    }
}
