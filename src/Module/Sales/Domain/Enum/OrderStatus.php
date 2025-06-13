<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\Enum;

use App\Core\Enum\EnumArraySerializableTrait;
use App\Core\Enum\EnumJsonSerializableTrait;

enum OrderStatus: string
{
    use EnumArraySerializableTrait;

    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
        };
    }
}
