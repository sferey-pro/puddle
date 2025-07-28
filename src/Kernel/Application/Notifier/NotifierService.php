<?php

declare(strict_types=1);

namespace Kernel\Application\Notifier;

use Identity\Domain\ValueObject\Identifier;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

/**
 * Service centralisé pour l'envoi de notifications.
 */
final class NotifierService
{
    /**
     * @var array<string, NotifierFactoryInterface>
     */
    private array $factories = [];

    public function __construct(
        private readonly NotifierInterface $symfonyNotifier,
        #[AutowireIterator('app.notifier_factory')]
        iterable $factories
    ) {
        foreach ($factories as $factory) {
            $this->registerFactory($factory);
        }
    }

    public function notify(
        string $type,
        Identifier $identifier,
        array $context = []
    ): void {
        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException(
                sprintf('No notifier factory registered for type "%s"', $type)
            );
        }

        $notification = $this->factories[$type]->createNotification($identifier, $context);

        $recipient = new Recipient($identifier->value());
        $this->symfonyNotifier->send($notification, $recipient);
    }

    private function registerFactory(NotifierFactoryInterface $factory): void
    {
        $this->factories[$factory->getNotificationType()] = $factory;
    }
}
