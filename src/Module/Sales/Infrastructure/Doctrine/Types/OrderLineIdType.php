<?php

declare(strict_types=1);

namespace App\Module\Sales\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use App\Module\Sales\Domain\ValueObject\OrderLineId;

final class OrderLineIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'order_line_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return OrderLineId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
