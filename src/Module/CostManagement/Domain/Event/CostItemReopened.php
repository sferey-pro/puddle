<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement émis lorsqu'un poste de coût couvert est réactivé à la modification d'une contribution.
 */
final readonly class CostItemReopened extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.reopened';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }
}
