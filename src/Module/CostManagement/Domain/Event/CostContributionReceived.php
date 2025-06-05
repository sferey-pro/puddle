<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Événement émis chaque fois qu'une contribution financière est ajoutée à un poste de coût.
 */
final class CostContributionReceived extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly CostItemId $costItemId,
        private readonly Money $contributionAmount,
        private readonly Money $newTotalCoveredAmount,
    ) {
        parent::__construct();
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function contributionAmount(): Money
    {
        return $this->contributionAmount;
    }

    public function newTotalCoveredAmount(): Money
    {
        return $this->newTotalCoveredAmount;
    }
}
