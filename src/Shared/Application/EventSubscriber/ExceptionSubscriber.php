<?php

declare(strict_types=1);

namespace App\Shared\Application\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // return the subscribed events, their methods and priorities
        return [
            ExceptionEvent::class => [
                ['processException', 10],
                ['logException', 0],
                ['notifyException', -10],
            ],
        ];
    }

    public function processException(ExceptionEvent $event): void
    {
        // ...
    }

    public function logException(ExceptionEvent $event): void
    {
        // ...
    }

    public function notifyException(ExceptionEvent $event): void
    {
        // ...
    }
}
