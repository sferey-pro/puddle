<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Bus;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Event\DomainEventInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class MessengerEventBus implements EventBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $eventBus)
    {
        $this->messageBus = $eventBus;
    }

    public function publish(DomainEventInterface ...$events): void
    {
        try {
            foreach ($events as $event) {
                $this->messageBus->dispatch(
                    (new Envelope($event))->with(new DispatchAfterCurrentBusStamp())
                );
            }
        } catch (HandlerFailedException $e) {
            if ($exception = current($e->getWrappedExceptions())) {
                throw $exception;
            }

            throw $e;
        }
    }
}
