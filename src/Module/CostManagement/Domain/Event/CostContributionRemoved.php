<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

/**
 * Événement émis chaque fois qu'une contribution financière est ajoutée à un poste de coût.
 */
final class CostContributionRemoved extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly CostItemId $costItemId,
        private readonly CostContributionId $costContributionId,
    ) {
        parent::__construct();
    }

    public function costItemId(): CostItemId
    {
        return $this->costItemId;
    }

    public function costContributionId(): CostContributionId
    {
        return $this->costContributionId;
    }
}
