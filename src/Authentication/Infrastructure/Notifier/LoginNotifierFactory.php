<?php

namespace Authentication\Infrastructure\Notifier;

use Authentication\Application\Notifier\LoginEmailNotification;
use Authentication\Application\Notifier\LoginSmsNotification;
use Authentication\Domain\Model\AccessCredential\OTPCredential;
use Authentication\Domain\Service\OTPServiceInterface;
use Authentication\Infrastructure\Security\UserProvider;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Notifier\NotifierFactoryInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

final class LoginNotifierFactory implements NotifierFactoryInterface
{
    public function __construct(
        private readonly LoginLinkHandlerInterface $loginLinkHandler,
        private readonly OTPServiceInterface $otpService,
        private readonly UserProvider $userProvider,
        private readonly RequestStack $requestStack
    ) {}

    public function getNotificationType(): string
    {
        return 'authentication.login';
    }

    public function createNotification(Identifier $identifier, array $context = []): Notification
    {
        $ipAddress = $this->requestStack->getCurrentRequest()?->getClientIp() ?? 'Unknown';

        return match($identifier->getType()) {
            'email' => $this->createEmailNotification($identifier, $context['userId'], $ipAddress),
            'phone' => $this->createSmsNotification($identifier, $context['credential']),
            default => throw new \LogicException('Unsupported identifier type for login')
        };
    }

    private function createEmailNotification(
        Identifier $identifier,
        UserId $userId,
        string $ipAddress
    ): LoginEmailNotification {
        // Charger le UserSecurity pour créer le LoginLink
        $userSecurity = $this->userProvider->loadUserByIdentifier($userId);

        // Créer le LoginLink via Symfony
        $loginLinkDetails = $this->loginLinkHandler->createLoginLink($userSecurity);

        return new LoginEmailNotification(
            loginLinkDetails: $loginLinkDetails,
            userEmail: $identifier->value(),
            ipAddress: $ipAddress
        );
    }

    private function createSmsNotification(
        Identifier $identifier,
        OTPCredential $credential
    ): LoginSmsNotification {
        return new LoginSmsNotification(
            otpCode: $credential->token,
            phoneNumber: $identifier->value(),
            expiresInMinutes: 10
        );
    }
}
