<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\In;

use Authentication\Infrastructure\Port\In\CredentialManagementPort;
use Authentication\Application\Query\GetUserCredentials;
use Authentication\Application\Query\GetLoginStatistics;
use Authentication\Application\Command\InvalidateCredential;
use Authentication\Domain\Exception\CredentialNotFoundException;
use Kernel\Application\Bus\CommandBusInterface;
use Kernel\Application\Bus\QueryBusInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\ValueObject\Identity\CredentialId;
use Psr\Log\LoggerInterface;

/**
 * Adapter pour les opérations de gestion des credentials.
 *
 * @context-boundary
 * Direction: IN (Admin/Support Tools → Authentication)
 * Type: Adapter (Implementation)
 * Protocol: Sync via Direct Method Call
 *
 * Traduit les appels administratifs en queries/commands internes
 * du contexte Authentication.
 *
 * @implements CredentialManagementPort
 */
final class CredentialManagementAdapter implements CredentialManagementPort
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - String userId → Query interne
     * - CredentialDTO[] → array structure simplifiée
     * - Filtre uniquement les credentials actifs
     */
    public function listUserCredentials(string $userId): array
    {
        $this->logger->info('Listing user credentials', [
            'userId' => $userId,
            'source' => 'admin_tools'
        ]);

        try {
            $query = new GetUserCredentials(
                userId: UserId::fromString($userId),
                includeExpired: false,
                includeRevoked: false
            );

            /** @var CredentialListDTO $result */
            $result = $this->queryBus->ask($query);

            return array_map(
                fn(CredentialDTO $credential) => [
                    'id' => $credential->id,
                    'type' => $credential->type,
                    'identifier' => $credential->identifier,
                    'createdAt' => $credential->createdAt->format('c'),
                    'expiresAt' => $credential->expiresAt?->format('c') ?? 'never',
                    'lastUsedAt' => $credential->lastUsedAt?->format('c'),
                    'usageCount' => $credential->usageCount
                ],
                $result->credentials
            );

        } catch (\Throwable $e) {
            $this->logger->error('Failed to list credentials', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);

            // Return empty array on error for admin tools
            return [];
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - Validation du format credentialId
     * - Dispatch command avec metadata admin
     */
    public function invalidateCredential(string $credentialId, string $reason): void
    {
        $this->logger->warning('Invalidating credential', [
            'credentialId' => $credentialId,
            'reason' => $reason,
            'source' => 'admin_action'
        ]);

        try {
            $command = new InvalidateCredential(
                credentialId: CredentialId::fromString($credentialId),
                reason: $reason,
                invalidatedBy: 'admin', // Could be enhanced with actual admin ID
                metadata: [
                    'action_source' => 'credential_management_port',
                    'timestamp' => (new \DateTimeImmutable())->format('c')
                ]
            );

            $this->commandBus->dispatch($command);

        } catch (CredentialNotFoundException $e) {
            // Log but don't throw - credential might already be deleted
            $this->logger->info('Credential not found for invalidation', [
                'credentialId' => $credentialId
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('Failed to invalidate credential', [
                'credentialId' => $credentialId,
                'error' => $e->getMessage()
            ]);

            throw new \RuntimeException(
                'Unable to invalidate credential',
                previous: $e
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - Agrégation de multiples queries
     * - Calcul des statistiques
     * - Format de réponse standardisé
     */
    public function getUserLoginStatistics(
        string $userId,
        \DateTimeInterface $since
    ): array {
        try {
            $query = new GetLoginStatistics(
                userId: UserId::fromString($userId),
                since: \DateTimeImmutable::createFromInterface($since),
                until: new \DateTimeImmutable()
            );

            /** @var LoginStatisticsDTO $stats */
            $stats = $this->queryBus->ask($query);

            return [
                'attempts' => $stats->totalAttempts,
                'successes' => $stats->successfulLogins,
                'failures' => $stats->failedAttempts,
                'lastLogin' => $stats->lastSuccessfulLogin?->format('c'),
                'suspiciousActivities' => $stats->suspiciousActivities,
                'uniqueDevices' => $stats->uniqueDevices,
                'averageTimeBetweenLogins' => $stats->averageTimeBetweenLogins
            ];

        } catch (\Throwable $e) {
            $this->logger->error('Failed to get login statistics', [
                'userId' => $userId,
                'since' => $since->format('c'),
                'error' => $e->getMessage()
            ]);

            // Return zero stats on error
            return [
                'attempts' => 0,
                'successes' => 0,
                'failures' => 0,
                'lastLogin' => null
            ];
        }
    }
}
