<?php

namespace App\Module\Auth\Infrastructure\Symfony\Notifier;

use App\Auth\Domain\Otp\OtpGeneratorInterface;
use App\Module\Auth\Application\Notifier\WelcomeNotification;
use App\Module\Auth\Domain\AccessCredential\OtpCredential;
use App\Module\Auth\Domain\Notification\NotificationChannel;
use App\Module\Auth\Domain\Notification\WelcomeNotifierInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

final readonly class SmsWelcomeNotifier implements WelcomeNotifierInterface
{
    public function __construct(
        private OtpGeneratorInterface $otpGenerator,
        private UserRepositoryInterface $userAccountRepository,
        private NotifierInterface $notifier
    ) {}

    public function notify(UserId $userId): void
    {
        $userAccount = $this->userAccountRepository->ofId($userId);

        $plainOtp = $this->otpGenerator->generateForUser($userId);
        $credential = new OtpCredential($plainOtp);

        $notification = new WelcomeNotification(
            $credential,
            '🎉 Bienvenue sur Puddle !'
        );

        $recipient = new Recipient((string) $userAccount->phone);

        $this->notifier->send($notification, $recipient);
    }

    public static function getSupportedChannel(): string
    {
        return NotificationChannel::Sms->value;
    }
}
