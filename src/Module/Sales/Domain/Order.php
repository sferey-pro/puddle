<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain;

use App\Module\Sales\Domain\Enum\OrderStatus;
use App\Module\Sales\Domain\Event\OrderCreated;
use App\Module\Sales\Domain\ValueObject\OrderId;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEventTrait;
use App\Shared\Domain\Service\SystemTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Order extends AggregateRoot
{
    use DomainEventTrait;

    /** @var Collection<int, OrderLine> */
    private Collection $orderLines;
    private OrderStatus $status;
    private \DateTimeImmutable $createdAt;

    private function __construct(
        private readonly OrderId $id,
        private readonly UserId $userId,
    ) {
        $this->orderLines = new ArrayCollection();
        $this->status = OrderStatus::PENDING;
        $this->createdAt = SystemTime::now();
    }

    public static function create(UserId $userId, array $orderLines): self
    {
        $id = OrderId::generate();
        $order = new self($id, $userId);

        $total = Money::fromFloat(0);
        $eventLines = [];

        foreach ($orderLines as $line) {
            $order->addLine($line);

            $lineTotal = $line->calculateTotal();
            $total = $total->add($lineTotal);

            $eventLines[] = [
                'productId' => (string) $line->productId,
                'quantity' => $line->quantity,
                'unitPriceAmount' => $line->unitPrice->getAmount(),
                'unitPriceCurrency' => $line->unitPrice->getCurrency(),
                'totalAmount' => $lineTotal->getAmount(),
                'totalCurrency' => $lineTotal->getCurrency(),
            ];
        }

        $order->recordDomainEvent(new OrderCreated(
            (string) $id,
            (string) $userId,
            $eventLines,
            $total->getAmount(),
            $total->getCurrency()
        ));

        return $order;
    }

    public function addLine(OrderLine $line): void
    {
        if (!$this->orderLines->contains($line)) {
            $this->orderLines->add($line);
            $line->setOrder($this);
        }
    }

    public function id(): OrderId
    {
        return $this->id;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function orderLines(): Collection
    {
        return $this->orderLines;
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function calculateTotal(): Money
    {
        $total = Money::fromFloat(0);
        foreach ($this->orderLines as $line) {
            $total = $total->add($line->calculateTotal());
        }

        return $total;
    }
}
