<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\Out;

use Authentication\Infrastructure\Port\Out\IdentityContextPort;
use Authentication\Infrastructure\Port\Out\DTO\UserIdentifierDTO;
use SharedKernel\Domain\Service\IdentityContextInterface;
use SharedKernel\Domain\Service\IdentifierAnalyzerInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Authentication\Domain\Exception\IdentityContextException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Adapter pour la communication avec le contexte Identity.
 *
 * @context-boundary
 * Direction: OUT (Authentication → Identity)
 * Type: Adapter (Implementation)
 * Protocol: Sync via ACL Interface
 *
 * Traduit les besoins d'Authentication vers l'ACL du contexte Identity
 * avec cache et gestion d'erreurs.
 *
 * @implements IdentityContextPort
 */
final readonly class IdentityContextAdapter implements IdentityContextPort
{
    private const CACHE_TTL = 600; // 10 minutes
    private const CACHE_PREFIX = 'auth.identity.';

    public function __construct(
        private(set) IdentityContextInterface $context,
        private(set) IdentifierAnalyzerInterface $analyzer,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger
    ) {}


    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - String identifier → résolution via ACL
     * - IdentityDTO → string userId
     * - Cache du mapping identifier->userId
     */
    public function resolveUserIdFromIdentifier(string $identifier): ?string
    {
        $cacheKey = self::CACHE_PREFIX . 'resolve.' . md5($identifier);

        try {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::CACHE_TTL);

                $this->logger->debug('Resolving identifier to userId', [
                    'identifier' => $this->maskIdentifier($identifier)
                ]);

                $userIdentity = $this->context->resolveIdentifierOrThrow($identifier);

                if ($userIdentity === null) {
                    $this->logger->info('Identifier not found', [
                        'identifier' => $this->maskIdentifier($identifier)
                    ]);
                    return null;
                }

                return (string) $userIdentity->userId;
            });

        } catch (\Throwable $e) {
            $this->logger->error('Failed to resolve identifier', [
                'identifier' => $this->maskIdentifier($identifier),
                'error' => $e->getMessage()
            ]);

            throw new IdentityContextException(
                'Unable to resolve user identifier',
                previous: $e
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - Vérification d'appartenance via ACL
     * - Optimisation : check cache de résolution d'abord
     */
    public function userOwnsIdentifier(string $userId, string $identifier): bool
    {
        try {
            // Quick check via resolution cache
            $resolvedUserId = $this->resolveUserIdFromIdentifier($identifier);

            if ($resolvedUserId === null) {
                return false;
            }

            return $resolvedUserId === $userId;

        } catch (IdentityContextException $e) {
            // On error, do explicit check
            try {
                $ownership = $this->context->verifyIdentifierOwnership(
                    UserId::fromString($userId),
                    $identifier
                );

                return $ownership->isOwned;

            } catch (\Throwable $fallbackError) {
                $this->logger->error('Failed to verify identifier ownership', [
                    'userId' => $userId,
                    'error' => $fallbackError->getMessage()
                ]);

                // Fail closed - assume not owned
                return false;
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - IdentityCollectionDTO → array simple
     * - Enrichissement avec type détecté
     */
    public function getUserIdentifiers(string $userId): array
    {
        $cacheKey = self::CACHE_PREFIX . 'identifiers.' . $userId;

        try {
            return $this->cache->get($cacheKey, function (ItemInterface $item) use ($userId) {
                $item->expiresAfter(self::CACHE_TTL);

                $identities = $this->context->getUserIdentities(
                    UserId::fromString($userId)
                );

                return array_map(
                    fn(UserIdentifierDTO $identity) => [
                        'type' => $identity->type,
                        'value' => $identity->value,
                        'verified' => $identity->isVerified,
                        'isPrimary' => $identity->isPrimary,
                        'addedAt' => $identity->addedAt->format('c')
                    ],
                    $identities->identifiers
                );
            });

        } catch (\Throwable $e) {
            $this->logger->error('Failed to get user identifiers', [
                'userId' => $userId,
                'error' => $e->getMessage()
            ]);

            // Return empty array on error
            return [];
        }
    }

    /**
     * {@inheritDoc}
     *
     * Traduction:
     * - Utilise le service d'analyse local
     * - Fallback sur l'ACL si nécessaire
     */
    public function getIdentifierType(string $identifier): string
    {
        try {
            // Try local analyzer first (faster)
            $type = $this->identifierAnalyzer->detectType($identifier);

            if ($type !== 'unknown') {
                return $type;
            }

            // Fallback to Identity context for complex types
            $analysis = $this->context->analyzeIdentifier($identifier);

            return $analysis->type ?? 'unknown';

        } catch (\Throwable $e) {
            $this->logger->warning('Failed to determine identifier type', [
                'identifier' => $this->maskIdentifier($identifier),
                'error' => $e->getMessage()
            ]);

            return 'unknown';
        }
    }

    /**
     * Masque un identifiant pour les logs.
     */
    private function maskIdentifier(string $identifier): string
    {
        if (str_contains($identifier, '@')) {
            // Email: show first 3 chars + domain
            [$local, $domain] = explode('@', $identifier);
            return substr($local, 0, 3) . '***@' . $domain;
        }

        // Phone or other: show first 3 and last 2
        $length = strlen($identifier);
        if ($length > 5) {
            return substr($identifier, 0, 3) . '***' . substr($identifier, -2);
        }

        return '***';
    }
}
