<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\Out;

use Authentication\Infrastructure\Port\Out\AccountContextPort;
use SharedKernel\Domain\Service\AccountContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\Exception\AccountNotFoundException as ACLAccountNotFoundException;
use Authentication\Domain\Exception\AccountContextException;
use Authentication\Infrastructure\Port\Out\DTO\AccountInfoDTO;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Adapter pour la communication avec le contexte Account.
 *
 * @context-boundary
 * Direction: OUT (Authentication → Account)
 * Type: Adapter (Implementation)
 * Protocol: Sync via ACL Interface
 *
 * Cet adapter traduit les besoins d'Authentication vers l'interface ACL
 * du contexte Account. Il gère le caching, la résilience et la traduction
 * des exceptions.
 *
 * @implements AccountContextPort
 */
final class AccountContextAdapter implements AccountContextPort
{
    private const CACHE_TTL = 300; // 5 minutes

    public function __construct(
        private readonly AccountContextInterface $accountContext,
        private readonly AdapterInterface $cache,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - String userId → UserId VO pour l'appel ACL
     * - ACL DTO → Authentication DTO
     * - Gestion d'erreur et traduction d'exception
     */
    public function getAccountInfo(string $userId): ?AccountInfoDTO
    {
        try {
            $accountData = $this->accountContext->getAccountDetails(
                UserId::fromString($userId)
            );

            // Traduction : ACL DTO → Authentication DTO
            return new AccountInfoDTO(
                userId: (string) $accountData->id,
                status: $accountData->status->value,
                email: $accountData->email?->value(),
                phone: $accountData->phone?->value(),
                isVerified: $accountData->verifiedAt !== null,
                suspendedUntil: $accountData->suspendedUntil
            );

        } catch (\Throwable $e) {
            $this->logger->error('Failed to get account info', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);

            throw new AccountContextException(
                'Unable to retrieve account info',
                previous: $e
            );
        }

    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - String userId → UserId VO pour l'appel ACL
     * - AccountStatus enum → boolean
     * - Mise en cache du résultat pour performance
     * - Gestion d'erreur avec fallback sur false
     */
    public function isAccountActive(string $userId): bool
    {
        $cacheKey = sprintf('auth.account.active.%s', $userId);

        // Try cache first
        $cached = $this->cache->getItem($cacheKey);
        if ($cached->isHit()) {
            return $cached->get();
        }

        try {
            $this->logger->debug('Checking account status', ['userId' => $userId]);

            $accountInfo = $this->accountContext->getAccountStatus(
                UserId::fromString($userId)
            );

            $isActive = $accountInfo->status === 'active';

            // Cache the result
            $cached->set($isActive);
            $cached->expiresAfter(self::CACHE_TTL);
            $this->cache->save($cached);

            return $isActive;

        } catch (ACLAccountNotFoundException $e) {
            $this->logger->info('Account not found', ['userId' => $userId]);
            return false;

        } catch (\Throwable $e) {
            $this->logger->error('Failed to check account status', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);

            // Fail open - assume inactive on error
            return false;
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - ContactInfoDTO → array structure attendue
     * - Gestion des valeurs nulles
     * - Pas de cache car données sensibles
     */
    public function getAccountContacts(string $userId): array
    {
        try {
            $contactInfo = $this->accountContext->getContactInformation(
                UserId::fromString($userId)
            );

            return [
                'email' => $contactInfo->email?->value(),
                'phone' => $contactInfo->phone?->value(),
                'preferredChannel' => $contactInfo->preferredChannel ?? 'email'
            ];

        } catch (ACLAccountNotFoundException $e) {
            throw new AccountNotFoundException(
                sprintf('Account %s not found', $userId),
                previous: $e
            );

        } catch (\Throwable $e) {
            $this->logger->error('Failed to get account contacts', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);

            throw new AccountContextException(
                'Unable to retrieve account contacts',
                previous: $e
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * Fire-and-forget pattern - pas d'attente de réponse
     */
    public function notifySuccessfulLogin(string $userId, array $metadata): void
    {
        try {
            $this->accountContext->recordLoginEvent(
                UserId::fromString($userId),
                $metadata
            );

            // Invalidate cache after login
            $this->cache->deleteItem(sprintf('auth.account.active.%s', $userId));

        } catch (\Throwable $e) {
            // Log but don't fail the login process
            $this->logger->warning('Failed to notify login event', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function reportSuspiciousActivity(
        string $userId,
        int $failedAttempts,
        string $reason
    ): void {
        try {
            $this->logger->warning('Reporting suspicious activity', [
                'userId' => $userId,
                'attempts' => $failedAttempts,
                'reason' => $reason
            ]);

            $this->accountContext->flagSuspiciousActivity(
                UserId::fromString($userId),
                [
                    'failedAttempts' => $failedAttempts,
                    'reason' => $reason,
                    'timestamp' => (new \DateTimeImmutable())->format('c')
                ]
            );

        } catch (\Throwable $e) {
            // Critical security event - log with high priority
            $this->logger->critical('Failed to report suspicious activity', [
                'userId' => $userId,
                'attempts' => $failedAttempts,
                'error' => $e->getMessage()
            ]);
        }
    }
}
