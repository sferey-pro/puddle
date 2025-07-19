<?php

declare(strict_types=1);

namespace Kernel\Domain\Port;

namespace Kernel\Infrastructure\Notification;

use Kernel\Domain\Port\NotificationInterface;
use Kernel\Domain\Port\NotificationMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Message\SmsMessage;

final class SMSNotificationAdapter implements NotificationInterface
{
    public function __construct(
        private readonly TexterInterface $texter
    ) {}

    public function send(NotificationMessage $message): void
    {
        if (!$this->supports($message->channel)) {
            throw new \InvalidArgumentException('This adapter only supports SMS');
        }

        $template = $message->parameters['template'];
        unset($message->parameters['template']);

        $sms = new SmsMessage(
            $message->recipient,
            sprintf($template, ...$message->parameters)
        );

        $this->texter->send($sms);
    }

    public function supports(string $channel): bool
    {
        return $channel === 'sms';
    }
}
