<?php

declare(strict_types=1);

namespace Authentication\Domain\Model;

use Account\Core\Domain\Model\Account;
use Authentication\Domain\ValueObject\LoginAttemptHistory;
use Identity\Domain\ValueObject\Identifier;

/**
 * Représente une demande de connexion avec tout le contexte nécessaire
 * pour que les Specifications puissent prendre une décision.
 */
final readonly class LoginRequest
{
    /**
     * @param LoginAttempt[] $recentAttemptsByIdentifier Tentatives récentes pour cet identifiant
     * @param LoginAttempt[] $recentAttemptsByIp Tentatives récentes depuis cette IP
     */
    public function __construct(
        public Identifier $identifier,
        public ?Account $account,
        public string $ipAddress,
        public string $userAgent,
        public array $recentAttemptsByIdentifier = [],
        public array $recentAttemptsByIp = [],
        public ?\DateTimeImmutable $requestedAt = null,
    ) {}
}
