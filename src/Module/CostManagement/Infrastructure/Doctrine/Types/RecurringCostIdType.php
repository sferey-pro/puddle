<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;

final class RecurringCostIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'recurring_cost_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return RecurringCostId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
