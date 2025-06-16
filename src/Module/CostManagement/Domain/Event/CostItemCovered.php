<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement émis lorsqu'un poste de coût atteint ou dépasse son montant cible.
 */
final readonly class CostItemCovered extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private Money $totalCoveredAmount,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.covered';
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function totalCoveredAmount(): Money
    {
        return $this->totalCoveredAmount;
    }
}
