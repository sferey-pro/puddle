<?php

namespace Account\Registration\Infrastructure\Notifier;

use Account\Registration\Application\Notifier\WelcomeEmailNotification;
use Account\Registration\Application\Notifier\WelcomeSmsNotification;
use Authentication\Infrastructure\Security\UserProvider;
use Authentication\Infrastructure\Service\OTPService;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Application\Notifier\NotifierFactoryInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

final class WelcomeNotifierFactory implements NotifierFactoryInterface
{
    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly UserProvider $userProvider,
        private readonly OTPService $otpService
    ) {}

    public function getNotificationType(): string
    {
        return 'registration.welcome';
    }

    public function createNotification(Identifier $identifier, array $context = []): Notification
    {
        return match($identifier->getClass()) {
            EmailIdentity::class => new WelcomeEmailNotification(
                magicLinkUrl: $this->createMagicLink($identifier),
                userEmail: $identifier->value()
            ),
            PhoneIdentity::class => new WelcomeSmsNotification(
                otpCode: $this->otpService->generateOTP($identifier->value()),
                phoneNumber: $identifier->value()
            ),
            default => throw new \LogicException('Unsupported identifier type')
        };
    }

    private function createMagicLink(Identifier $identifier): string
    {
        $userSecurity = $this->userProvider->loadUserByIdentifier($identifier->value());
        $loginLink = $this->loginLinkHandler->createLoginLink($userSecurity);

        return $loginLink->getUrl();
    }
}
