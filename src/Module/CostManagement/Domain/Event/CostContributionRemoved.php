<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\CostManagement\Domain\ValueObject\CostContributionId;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

/**
 * Événement émis chaque fois qu'une contribution financière est ajoutée à un poste de coût.
 */
final readonly class CostContributionRemoved extends DomainEvent
{
    public function __construct(
        private CostItemId $costItemId,
        private CostContributionId $costContributionId,
    ) {
        parent::__construct($this->costItemId);
    }

    public static function eventName(): string
    {
        return 'cost_management.costitem.contribution_removed';
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
