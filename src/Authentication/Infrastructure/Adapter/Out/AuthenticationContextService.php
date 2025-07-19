<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\Out;

use Authentication\Domain\Model\Session;
use Authentication\Domain\Model\AuthenticationAttempt;
use Authentication\Domain\Repository\SessionRepositoryInterface;
use Authentication\Domain\Repository\AuthenticationAttemptRepositoryInterface;
use Authentication\Domain\Repository\TrustedDeviceRepositoryInterface;
use SharedKernel\Domain\Service\AuthenticationContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\DTO\Authentication\SessionInfoDTO;
use SharedKernel\Domain\DTO\Authentication\AuthenticationHistoryDTO;
use SharedKernel\Domain\DTO\Authentication\ActiveSessionsDTO;
use SharedKernel\Domain\DTO\Authentication\AuthenticationAttemptDTO;
use Symfony\Component\Messenger\MessageBusInterface;
use Authentication\Domain\Event\AllSessionsInvalidatedEvent;

final class AuthenticationContextService implements AuthenticationContextInterface
{
    public function __construct(
        private readonly SessionRepositoryInterface $sessionRepository,
        private readonly AuthenticationAttemptRepositoryInterface $attemptRepository,
        private readonly TrustedDeviceRepositoryInterface $trustedDeviceRepository,
        private readonly MessageBusInterface $eventBus
    ) {}

    public function getCurrentSession(string $sessionId): ?SessionInfoDTO
    {
        $session = $this->sessionRepository->findBySessionId($sessionId);

        if ($session === null || $session->isExpired()) {
            return null;
        }

        return $this->mapSessionToDTO($session);
    }

    public function getActiveSessions(UserId $userId): ActiveSessionsDTO
    {
        $sessions = $this->sessionRepository->findActiveByUserId($userId);

        $sessionDTOs = array_map(
            fn(Session $session) => $this->mapSessionToDTO($session),
            $sessions
        );

        return new ActiveSessionsDTO(
            userId: $userId,
            sessions: $sessionDTOs,
            totalCount: count($sessionDTOs)
        );
    }

    public function getAuthenticationHistory(
        UserId $userId,
        int $limit = 10
    ): AuthenticationHistoryDTO {
        $attempts = $this->attemptRepository->findRecentByUserId(
            $userId,
            $limit
        );

        $attemptDTOs = array_map(
            fn(AuthenticationAttempt $attempt) => $this->mapAttemptToDTO($attempt),
            $attempts
        );

        return new AuthenticationHistoryDTO(
            userId: $userId,
            attempts: $attemptDTOs,
            totalCount: count($attemptDTOs)
        );
    }

    public function hasActiveSession(UserId $userId): bool
    {
        return $this->sessionRepository->hasActiveSession($userId);
    }

    public function isDeviceTrusted(UserId $userId, string $deviceId): bool
    {
        $device = $this->trustedDeviceRepository->findByAccountAndDeviceId(
            $userId,
            $deviceId
        );

        return $device !== null && $device->isStillTrusted();
    }

    public function invalidateAllSessions(UserId $userId): void
    {
        // Récupérer toutes les sessions actives
        $sessions = $this->sessionRepository->findActiveByUserId($userId);

        // Invalider chaque session
        foreach ($sessions as $session) {
            $session->invalidate();
            $this->sessionRepository->save($session);
        }

        // Émettre un événement pour notifier les autres systèmes
        $this->eventBus->dispatch(new AllSessionsInvalidatedEvent(
            userId: $userId,
            invalidatedAt: new \DateTimeImmutable(),
            sessionCount: count($sessions)
        ));
    }

    private function mapSessionToDTO(Session $session): SessionInfoDTO
    {
        return new SessionInfoDTO(
            sessionId: $session->getId(),
            userId: $session->getUserId(),
            ipAddress: $session->getIpAddress(),
            userAgent: $session->getUserAgent(),
            deviceId: $session->getDeviceId(),
            createdAt: $session->getCreatedAt(),
            lastActivityAt: $session->getLastActivityAt(),
            expiresAt: $session->getExpiresAt(),
            is2FAVerified: $session->is2FAVerified()
        );
    }

    private function mapAttemptToDTO(AuthenticationAttempt $attempt): AuthenticationAttemptDTO
    {
        return new AuthenticationAttemptDTO(
            attemptedAt: $attempt->getAttemptedAt(),
            success: $attempt->isSuccessful(),
            ipAddress: $attempt->getIpAddress(),
            userAgent: $attempt->getUserAgent(),
            failureReason: $attempt->getFailureReason(),
            method: $attempt->getMethod() // password, oauth, magic-link, etc.
        );
    }
}
