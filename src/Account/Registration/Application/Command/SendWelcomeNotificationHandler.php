<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use Account\Registration\Domain\Event\WelcomeNotificationSent;
use Kernel\Application\Bus\EventBusInterface;
use Kernel\Application\Notifier\NotifierService;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final readonly class SendWelcomeNotificationHandler
{
    public function __construct(
        private NotifierService $notifierService,
        private EventBusInterface $eventBus
    ) {
    }

    public function __invoke(SendWelcomeNotification $command): void
    {
        $this->notifierService->notify(
            type: 'registration.welcome',
            identifier: $command->identifier,
            context: ['userId' => $command->userId]
        );

        $this->eventBus->publish(new WelcomeNotificationSent(
            userId: $command->userId,
            channel: $command->identifier->getType()
        ));
    }
}
