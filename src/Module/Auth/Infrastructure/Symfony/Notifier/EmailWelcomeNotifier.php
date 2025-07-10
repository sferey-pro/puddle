<?php

namespace App\Module\Auth\Infrastructure\Symfony\Notifier;

use App\Module\Auth\Application\Notifier\WelcomeNotification;
use App\Module\Auth\Domain\AccessCredential\MagicLinkCredential;
use App\Module\Auth\Domain\Notification\NotificationChannel;
use App\Module\Auth\Domain\Notification\WelcomeNotifierInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\LoginLinkManager;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final readonly class EmailWelcomeNotifier implements WelcomeNotifierInterface
{
    public function __construct(
        private UserRepositoryInterface $userAccountRepository,
        private LoginLinkManager $loginLinkManager,
        private NotifierInterface $notifier
    ) {}

    public function notify(UserId $userId): void
    {
        $userAccount = $this->userAccountRepository->ofId($userId);

        $loginLinkDetails = $this->loginLinkManager->createForNewUser($userAccount->email);
        $credential = new MagicLinkCredential($loginLinkDetails->details->url());

        $notification = new WelcomeNotification(
            $credential,
            '🎉 Bienvenue sur Puddle !'
        );

        $recipient = new Recipient((string) $userAccount->email);

        $this->notifier->send($notification, $recipient);
    }

    public static function getSupportedChannel(): string
    {
        return NotificationChannel::Email->value;
    }
}
