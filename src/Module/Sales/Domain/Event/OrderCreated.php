<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\Event;

use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\DomainEventInterface;

final class OrderCreated extends DomainEvent implements DomainEventInterface
{
    public function __construct(
        private readonly string $orderId,
        private readonly string $userId,
        private readonly array $orderLines,
        private readonly float $totalAmount,
        private readonly string $currency,
    ) {
        parent::__construct();
    }

    public static function eventName(): string
    {
        return 'sales.order.created';
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function orderLines(): array
    {
        return $this->orderLines;
    }

    public function totalAmount(): float
    {
        return $this->totalAmount;
    }

    public function currency(): string
    {
        return $this->currency;
    }
}
