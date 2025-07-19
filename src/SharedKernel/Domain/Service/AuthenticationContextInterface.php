<?php

declare(strict_types=1);

namespace SharedKernel\Domain\Service;

use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\DTO\Authentication\SessionInfoDTO;
use SharedKernel\Domain\DTO\Authentication\AuthenticationHistoryDTO;
use SharedKernel\Domain\DTO\Authentication\ActiveSessionsDTO;

interface AuthenticationContextInterface
{
    /**
     * Informations de session pour autres contextes
     */
    public function getCurrentSession(string $sessionId): ?SessionInfoDTO;

    /**
     * Sessions actives d'un compte
     */
    public function getActiveSessions(UserId $userId): ActiveSessionsDTO;

    /**
     * Historique d'authentification
     */
    public function getAuthenticationHistory(
        UserId $userId,
        int $limit = 10
    ): AuthenticationHistoryDTO;

    /**
     * Vérifications de sécurité
     */
    public function hasActiveSession(UserId $userId): bool;
    public function isDeviceTrusted(UserId $userId, string $deviceId): bool;

    /**
     * Actions métier
     */
    public function invalidateAllSessions(UserId $userId): void;
}
