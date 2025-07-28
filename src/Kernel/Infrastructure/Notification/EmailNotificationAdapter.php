<?php

declare(strict_types=1);

namespace Kernel\Domain\Port;

namespace Kernel\Infrastructure\Notification;

use Kernel\Domain\Port\NotificationInterface;
use Kernel\Domain\Port\NotificationMessage;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

final class EmailNotificationAdapter implements NotificationInterface
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly Environment $twig
    ) {}

    public function send(NotificationMessage $message): void
    {
        if (!$this->supports($message->channel)) {
            throw new \InvalidArgumentException('This adapter only supports email');
        }

        $html = $this->twig->render($message->template, $message->parameters);

        $email = (new Email())
            ->to($message->recipient)
            ->subject($message->parameters['subject'] ?? 'Notification')
            ->html($html);

        $this->mailer->send($email);
    }

    public function supports(string $channel): bool
    {
        return $channel === 'email';
    }
}
