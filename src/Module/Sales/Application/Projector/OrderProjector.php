<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\Projector;

use App\Module\Sales\Application\ReadModel\OrderLineView;
use App\Module\Sales\Application\ReadModel\OrderView;
use App\Module\Sales\Application\ReadModel\Repository\OrderViewRepositoryInterface;
use App\Module\Sales\Domain\Enum\OrderStatus;
use App\Module\Sales\Domain\Event\OrderCreated;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderProjector implements EventSubscriberInterface
{
    public function __construct(
        private readonly OrderViewRepositoryInterface $orderViewRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OrderCreated::class => 'onOrderCreated',
        ];
    }

    public function onOrderCreated(OrderCreated $event): void
    {
        $orderView = new OrderView(
            $event->orderId(),
            $event->userId(),
            OrderStatus::PENDING->value,
            $event->totalAmount(),
            $event->currency(),
            $event->occurredOn()
        );

        foreach ($event->orderLines() as $line) {
            $orderView->orderLines[] = new OrderLineView(
                $line['productId'],
                $line['quantity'],
                $line['unitPriceAmount'],
                $line['currency'],
                $line['totalAmount']
            );
        }

        $this->orderViewRepository->save($orderView);
    }
}
