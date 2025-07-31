<?php

declare(strict_types=1);

namespace Authentication\Domain\Specification;

use Authentication\Domain\Model\LoginAttempt;
use Authentication\Domain\Model\LoginRequest;
use Kernel\Application\Clock\ClockInterface;
use Kernel\Domain\Specification\CompositeSpecification;

/**
 * Vérifie que les tentatives de connexion respectent le rate limiting
 * en utilisant le modèle LoginAttempt existant.
 */
final class LoginAttemptRateLimitSpecification extends CompositeSpecification
{
    // Configuration métier pour MVP
    private const int MAX_ATTEMPTS_PER_IDENTIFIER = 5;  // 5 tentatives par identifiant
    private const int MAX_ATTEMPTS_PER_IP = 20;         // 20 tentatives par IP
    private const int RATE_LIMIT_WINDOW_MINUTES = 15;   // Fenêtre de 15 minutes
    private const int LOCKOUT_DURATION_MINUTES = 30;    // Blocage de 30 minutes

    // Backoff progressif (en secondes)
    private const array BACKOFF_DELAYS = [0, 1, 2, 5, 10, 30, 60];

    public function __construct(
        private readonly ClockInterface $clock,
    ) {}

    public function isSatisfiedBy(mixed $candidate): bool
    {
        if (!$candidate instanceof LoginRequest) {
            return false;
        }

        // 1. Vérifier le rate limiting par identifiant
        if (!$this->isIdentifierRateLimitRespected($candidate)) {
            return false;
        }

        // 2. Vérifier le rate limiting par IP
        if (!$this->isIpRateLimitRespected($candidate)) {
            return false;
        }

        // 3. Vérifier le backoff delay
        if (!$this->isBackoffDelayRespected($candidate)) {
            return false;
        }

        // 4. Vérifier si pas en période de blocage
        if ($this->isInLockoutPeriod($candidate)) {
            return false;
        }

        return true;
    }

    public function failureReason(): ?string
    {
        return 'Trop de tentatives de connexion. Réessayez plus tard.';
    }

    private function isIdentifierRateLimitRespected(LoginRequest $request): bool
    {
        $recentAttempts = $this->filterRecentAttempts($request->recentAttemptsByIdentifier);

        return count($recentAttempts) < self::MAX_ATTEMPTS_PER_IDENTIFIER;
    }

    private function isIpRateLimitRespected(LoginRequest $request): bool
    {
        $recentAttempts = $this->filterRecentAttemptsFromIp(
            $request->recentAttemptsByIp,
            $request->ipAddress
        );

        return count($recentAttempts) < self::MAX_ATTEMPTS_PER_IP;
    }

    private function isBackoffDelayRespected(LoginRequest $request): bool
    {
        $lastFailedAttempt = $this->getLastFailedAttempt($request->recentAttemptsByIdentifier);

        if (!$lastFailedAttempt) {
            return true; // Première tentative
        }

        $consecutiveFailures = $this->countConsecutiveFailures($request->recentAttemptsByIdentifier);
        $requiredDelay = $this->getBackoffDelay($consecutiveFailures);

        $now = $this->clock->now();
        $timeSinceLastAttempt = $now->getTimestamp() - $lastFailedAttempt->attemptedAt->getTimestamp();

        return $timeSinceLastAttempt >= $requiredDelay;
    }

    private function isInLockoutPeriod(LoginRequest $request): bool
    {
        $consecutiveFailures = $this->countConsecutiveFailures($request->recentAttemptsByIdentifier);

        // Lockout après MAX_ATTEMPTS_PER_IDENTIFIER échecs consécutifs
        if ($consecutiveFailures < self::MAX_ATTEMPTS_PER_IDENTIFIER) {
            return false;
        }

        $lastFailedAttempt = $this->getLastFailedAttempt($request->recentAttemptsByIdentifier);
        if (!$lastFailedAttempt) {
            return false;
        }

        $now = $this->clock->now();
        $lockoutEnd = $lastFailedAttempt->attemptedAt->modify('+' . self::LOCKOUT_DURATION_MINUTES . ' minutes');

        return $now < $lockoutEnd;
    }

    /**
     * Filtre les tentatives récentes dans la fenêtre de rate limiting.
     *
     * @param LoginAttempt[] $attempts
     * @return LoginAttempt[]
     */
    private function filterRecentAttempts(array $attempts): array
    {
        return array_filter($attempts, function (LoginAttempt $attempt) {
            return $attempt->isRecentAttempt(self::RATE_LIMIT_WINDOW_MINUTES);
        });
    }

    /**
     * Filtre les tentatives récentes depuis une IP spécifique.
     *
     * @param LoginAttempt[] $attempts
     * @return LoginAttempt[]
     */
    private function filterRecentAttemptsFromIp(array $attempts, string $ipAddress): array
    {
        return array_filter($attempts, function (LoginAttempt $attempt) use ($ipAddress) {
            return $attempt->isFromSameIp($ipAddress)
                && $attempt->isRecentAttempt(self::RATE_LIMIT_WINDOW_MINUTES);
        });
    }

    /**
     * Trouve la dernière tentative échouée.
     *
     * @param LoginAttempt[] $attempts
     */
    private function getLastFailedAttempt(array $attempts): ?LoginAttempt
    {
        // Trier par date décroissante et prendre le premier échec
        $failedAttempts = array_filter($attempts, fn(LoginAttempt $attempt) => !$attempt->successful);

        if (empty($failedAttempts)) {
            return null;
        }

        usort($failedAttempts, fn(LoginAttempt $a, LoginAttempt $b) =>
            $b->attemptedAt <=> $a->attemptedAt
        );

        return $failedAttempts[0];
    }

    /**
     * Compte les échecs consécutifs depuis la dernière réussite.
     *
     * @param LoginAttempt[] $attempts
     */
    private function countConsecutiveFailures(array $attempts): int
    {
        if (empty($attempts)) {
            return 0;
        }

        // Trier par date décroissante
        usort($attempts, fn(LoginAttempt $a, LoginAttempt $b) =>
            $b->attemptedAt <=> $a->attemptedAt
        );

        $consecutiveFailures = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->successful) {
                break; // Arrêter au premier succès
            }
            $consecutiveFailures++;
        }

        return $consecutiveFailures;
    }

    private function getBackoffDelay(int $consecutiveFailures): int
    {
        $index = min($consecutiveFailures, count(self::BACKOFF_DELAYS) - 1);
        return self::BACKOFF_DELAYS[$index];
    }
}
