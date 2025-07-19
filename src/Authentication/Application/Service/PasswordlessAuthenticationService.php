<?php

declare(strict_types=1);

namespace Authentication\Application\Service;

use Authentication\Application\Command\RequestMagicLink;
use Authentication\Application\Command\RequestOTP\RequestOTP;
use Authentication\Domain\Exception\InvalidIdentifierException;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Application\Bus\CommandBusInterface;
use Psr\Log\LoggerInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;

/**
 * Service applicatif unifié pour l'authentification passwordless
 */
final readonly class PasswordlessAuthenticationService
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private IdentityContextInterface $identityContext,
        private LoggerInterface $logger
    ) {}

    /**
     * Point d'entrée unique depuis le formulaire LiveComponent.
     * Détermine le type d'identifiant et envoie la notification appropriée.
     *
     * @return string Type de notification envoyée ('magic_link' ou 'otp')
     * @throws InvalidIdentifierException
     * @throws TooManyAttemptsException
     */
    public function initiatePasswordlessAuthentication(
        string $identifier,
        string $ipAddress,
        ?string $userAgent = null
    ): string {
        $this->logger->info('Initiating passwordless authentication', [
            'identifier_length' => strlen($identifier),
            'ip' => $ipAddress
        ]);

        // 1. Résoudre et valider l'identifiant
        $identifierResult = $this->identityContext->resolveIdentifier($identifier);

        if ($identifierResult->isFailure()) {
            $this->logger->warning('Invalid identifier provided', [
                'error' => $identifierResult->error->getMessage()
            ]);

            throw InvalidIdentifierException::withMessage(
                'Please enter a valid email address or phone number.'
            );
        }

        $resolvedIdentifier = $identifierResult->value();

        // 2. Dispatcher la commande appropriée
        try {
            switch (true) {
                case $resolvedIdentifier instanceof EmailIdentity:
                    $this->commandBus->dispatch(new RequestMagicLink(
                        email: $resolvedIdentifier,
                        ipAddress: $ipAddress,
                        userAgent: $userAgent
                    ));

                    $this->logger->info('Magic link requested for email');
                    return 'magic_link';

                case $resolvedIdentifier instanceof PhoneIdentity:
                    $this->commandBus->dispatch(new RequestOTP(
                        phoneNumber: $resolvedIdentifier,
                        ipAddress: $ipAddress,
                        userAgent: $userAgent
                    ));

                    $this->logger->info('OTP requested for phone');
                    return 'otp';

                default:
                    throw InvalidIdentifierException::withMessage(
                        'Unsupported identifier type'
                    );
            }
        } catch (TooManyAttemptsException $e) {
            $this->logger->warning('Rate limit exceeded', [
                'identifier' => substr($identifier, -4),
                'retry_after' => $e->getRetryAfter()
            ]);
            throw $e;
        }
    }

    /**
     * Renvoie un code OTP (pour le bouton "Resend code").
     */
    public function resendOTP(string $phoneNumber, string $ipAddress): void
    {
        $this->logger->info('Resending OTP code', [
            'phone_suffix' => '...' . substr($phoneNumber, -4)
        ]);

        $this->commandBus->dispatch(new RequestOTP(
            phoneNumber: $phoneNumber,
            ipAddress: $ipAddress,
            userAgent: null
        ));
    }
}
