<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\In;

use Authentication\Infrastructure\Port\In\AuthenticationServicePort;
use Authentication\Application\Command\CreateMagicLink;
use Authentication\Application\Command\CreateOTP;
use Authentication\Application\Command\VerifyToken;
use Authentication\Application\Command\RevokeCredentials;
use Authentication\Domain\Exception\AuthenticationException;
use Kernel\Application\Bus\CommandBusInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Psr\Log\LoggerInterface;

/**
 * Adapter principal qui traduit les appels externes vers le domaine Authentication.
 *
 * @context-boundary
 * Direction: IN (External Contexts → Authentication)
 * Type: Adapter (Implementation)
 * Protocol: Sync via Direct Method Call
 *
 * Cet adapter agit comme un Anti-Corruption Layer, traduisant les types
 * primitifs externes en Value Objects du domaine et gérant les exceptions.
 *
 * @implements AuthenticationServicePort
 */
final class AuthenticationServiceAdapter implements AuthenticationServicePort
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - String userId → UserId Value Object
     * - String email → EmailIdentity Value Object
     * - Dispatch CreateMagicLinkCommand
     * - Log l'opération pour audit
     */
    public function createMagicLinkCredentials(
        string $userId,
        string $email,
        array $metadata = []
    ): void {
        $this->logger->info('Creating magic link credentials', [
            'userId' => $userId,
            'email' => $email,
            'source' => $metadata['source'] ?? 'unknown'
        ]);

        try {
            $command = new CreateMagicLink(
                userId: UserId::fromString($userId),
                identifier: EmailIdentity::fromString($email),
                metadata: $metadata
            );

            $this->commandBus->dispatch($command);

        } catch (AuthenticationException $e) {
            // Re-throw domain exceptions
            throw $e;
        } catch (\Throwable $e) {
            // Wrap infrastructure exceptions
            $this->logger->error('Failed to create magic link', [
                'error' => $e->getMessage(),
                'userId' => $userId
            ]);

            throw new CredentialCreationException(
                'Unable to create magic link credentials',
                previous: $e
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - String phoneNumber → PhoneIdentity Value Object
     * - Gestion du format international des numéros
     */
    public function createOTPCredentials(
        string $userId,
        string $phoneNumber,
        array $metadata = []
    ): void {
        $this->logger->info('Creating OTP credentials', [
            'userId' => $userId,
            'phone' => substr($phoneNumber, 0, -4) . '****', // Masked for security
            'source' => $metadata['source'] ?? 'unknown'
        ]);

        try {
            $command = new CreateOTP(
                userId: UserId::fromString($userId),
                phone: PhoneIdentity::fromString($phoneNumber),
                metadata: $metadata
            );

            $this->commandBus->dispatch($command);

        } catch (\Throwable $e) {
            $this->logger->error('Failed to create OTP', [
                'error' => $e->getMessage(),
                'userId' => $userId
            ]);

            throw new CredentialCreationException(
                'Unable to create OTP credentials',
                previous: $e
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - Résultat Command → Array structure attendue
     * - Gestion unifiée Magic Link et OTP
     */
    public function verifyAuthenticationToken(
        string $identifier,
        string $token
    ): array {
        try {
            $command = new VerifyToken(
                identifier: $identifier,
                token: $token
            );

            $result = $this->commandBus->dispatch($command);

            return [
                'userId' => (string) $result->userId,
                'isValid' => $result->isValid,
                'metadata' => [
                    'type' => $result->credentialType,
                    'verifiedAt' => $result->verifiedAt?->format('c')
                ]
            ];

        } catch (InvalidTokenException $e) {
            // Domain exception pass-through
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->error('Token verification failed', [
                'error' => $e->getMessage(),
                'identifier' => $identifier
            ]);

            throw new InvalidTokenException('Verification failed');
        }
    }

    /**
     * {@inheritDoc}
     */
    public function revokeAllUserCredentials(
        string $userId,
        string $reason
    ): void {
        $this->logger->warning('Revoking all user credentials', [
            'userId' => $userId,
            'reason' => $reason
        ]);

        $command = new RevokeCredentials(
            userId: UserId::fromString($userId),
            reason: $reason,
            revokeAll: true
        );

        $this->commandBus->dispatch($command);
    }
}
