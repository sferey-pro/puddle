<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\Sales\Domain\ValueObject\OrderId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final readonly class OrderCreated extends DomainEvent
{
    public function __construct(
        private OrderId $orderId,
        private UserId $userId,
        private array $orderLines,
        private Money $totalAmount,
        private string $currency,
    ) {
        parent::__construct($orderId);
    }

    public static function eventName(): string
    {
        return 'sales.order.created';
    }

    public function orderId(): OrderId
    {
        return $this->orderId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function orderLines(): array
    {
        return $this->orderLines;
    }

    public function totalAmount(): Money
    {
        return $this->totalAmount;
    }

    public function currency(): string
    {
        return $this->currency;
    }
}
