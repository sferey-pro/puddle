<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

/**
 * Événement émis lorsqu'un poste de coût est archivé.
 */
final readonly class CostItemArchived extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.archived';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }
}
