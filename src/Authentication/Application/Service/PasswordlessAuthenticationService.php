<?php

declare(strict_types=1);

namespace Authentication\Application\Service;

use Authentication\Application\Command\RequestMagicLink;
use Authentication\Application\Command\RequestOTP;
use Authentication\Domain\Exception\InvalidIdentifierException;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
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
        Identifier $identifier,
        string $ipAddress,
        ?string $userAgent = null
    ): void {
        $this->logger->info('Initiating passwordless authentication', [
            'identifier_length' => strlen($identifier->value()),
            'ip' => $ipAddress
        ]);

        // 2. Dispatcher la commande appropriée
        try {
            $command = match($identifier::class) {
                EmailIdentity::class => new RequestMagicLink(
                        email: $identifier,
                        ipAddress: $ipAddress,
                        userAgent: $userAgent
                ),
                PhoneIdentity::class => new RequestOTP(
                        identifier: $identifier,
                        ipAddress: $ipAddress,
                        userAgent: $userAgent
                ),
                default => throw new \LogicException('Unsupported identifier type')
            };

            $this->commandBus->dispatch($command);

        } catch (TooManyAttemptsException $e) {
            $this->logger->warning('Rate limit exceeded', [
                'identifier' => substr($identifier->value(), -4)
            ]);
            throw $e;
        }
    }

    /**
     * Renvoie un code OTP (pour le bouton "Resend code").
     */
    public function resendOTP(Identifier $identifier, string $ipAddress): void
    {
        $this->logger->info('Resending OTP code', [
            'phone_suffix' => '...' . substr($identifier->value(), -4)
        ]);

        $this->commandBus->dispatch(new RequestOTP(
            identifier: $identifier,
            ipAddress: $ipAddress,
            userAgent: null
        ));
    }
}
