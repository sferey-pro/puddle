<?php

declare(strict_types=1);

namespace Authentication\Application\Service;

use Account\Core\Domain\Model\Account;
use Authentication\Application\Command\RequestMagicLink;
use Authentication\Application\Command\RequestOTP\RequestOTP;
use Authentication\Application\Command\VerifyMagicLink;
use Authentication\Application\Command\VerifyOTP;
use Authentication\Domain\Exception\InvalidIdentifierException;
use Authentication\Domain\Specification\ValidIdentifierSpecification;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Application\Bus\CommandBusInterface;

/**
 * Service applicatif unifié pour l'authentification passwordless
 */
final readonly class PasswordlessAuthenticationService
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ValidIdentifierSpecification $identifierSpec,
        private IdentifierResolverInterface $identifierResolver
    ) {}

    /**
     * Point d'entrée unique depuis le formulaire
     */
    public function initiatePasswordlessAuthentication(
        string $identifier,
        string $ipAddress,
        ?string $userAgent = null
    ): string {
        // Résoudre l'identifiant
        $identifierResult = $this->identifierResolver->resolve($identifier);

        if ($identifierResult->isFailure()) {
            throw InvalidIdentifierException::withMessage($identifierResult->error->getMessage());
        }

        $identifier = $identifierResult->value();

        // Déterminer le type
        switch (true) {
            case $identifier instanceof EmailIdentity:
                $command = new RequestMagicLink(
                    email: $identifier,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );
                $returnType = 'magic_link';
                break;
            case $identifier instanceof PhoneIdentity:
                $command = new RequestOTP(
                    phoneNumber: $identifier,
                    ipAddress: $ipAddress,
                    userAgent: $userAgent
                );
                $returnType = 'otp';
                break;
            default:
                throw InvalidIdentifierException::withMessage('Invalid identifier type');
        }

        $this->commandBus->dispatch($command);
        return $returnType;
    }

    /**
     * Vérification du magic link
     */
    public function verifyMagicLink(
        string $token,
        string $ipAddress,
        ?string $userAgent = null
    ): Account {
        $command = new VerifyMagicLink(
            token: $token,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );

        return $this->commandBus->dispatch($command);
    }

    /**
     * Vérification OTP
     */
    public function verifyOTP(
        string $phoneNumber,
        string $code,
        string $ipAddress,
        ?string $userAgent = null
    ): Account {
        $command = new VerifyOTP(
            phoneNumber: $phoneNumber,
            code: $code,
            ipAddress: $ipAddress,
            userAgent: $userAgent
        );

        return $this->commandBus->dispatch($command);
    }
}
