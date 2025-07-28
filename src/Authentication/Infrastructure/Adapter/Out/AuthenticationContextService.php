<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\Out;

use Authentication\Domain\Repository\LoginAttemptRepositoryInterface;
use Authentication\Infrastructure\Security\UserSecurity;
use SharedKernel\Domain\Service\AuthenticationContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\DTO\Authentication\SessionInfoDTO;
use SharedKernel\Domain\DTO\Authentication\AuthenticationHistoryDTO;
use SharedKernel\Domain\DTO\Authentication\ActiveSessionsDTO;
use SharedKernel\Domain\DTO\Authentication\AuthenticationAttemptDTO;
use Symfony\Bundle\SecurityBundle\Security as SecurityBundleSecurity;
use Symfony\Component\HttpFoundation\RequestStack;

final class AuthenticationContextService implements AuthenticationContextInterface
{
    public function __construct(
        private readonly LoginAttemptRepositoryInterface $loginAttemptRepository,
        private readonly RequestStack $requestStack,
        private readonly SecurityBundleSecurity $security
    ) {}

    public function getCurrentSession(string $sessionId): ?SessionInfoDTO
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request?->getSession();

        if (!$session || $session->getId() !== $sessionId) {
            return null;
        }

        /**  @var UserSecurity $user */
        $user = $this->security->getUser();
        if (!$user) {
            return null;
        }

        return new SessionInfoDTO(
            sessionId: $session->getId(),
            userId: $user->getUserId(),
            ipAddress: $request->getClientIp(),
            userAgent: $request->headers->get('User-Agent', 'Unknown'),
            createdAt: new \DateTimeImmutable('@' . $session->getMetadataBag()->getCreated()),
            lastActivityAt: new \DateTimeImmutable('@' . $session->getMetadataBag()->getLastUsed()),
            expiresAt: new \DateTimeImmutable('+' . ini_get('session.gc_maxlifetime') . ' seconds'),
            is2FAVerified: false
        );
    }

    public function getActiveSessions(UserId $userId): ActiveSessionsDTO
    {
        // Avec Symfony, on ne peut pas facilement lister toutes les sessions d'un user
        // On retourne juste la session courante si elle appartient à cet utilisateur
        $sessions = [];

        /**  @var UserSecurity $currentUser */
        $currentUser = $this->security->getUser();
        if ($currentUser && $currentUser->getUserId()->equals($userId)) {
            $currentSession = $this->getCurrentSession(
                $this->requestStack->getSession()->getId()
            );
            if ($currentSession) {
                $sessions[] = $currentSession;
            }
        }

        return new ActiveSessionsDTO(
            userId: $userId,
            sessions: $sessions,
            totalCount: count($sessions)
        );
    }

    public function getAuthenticationHistory(
        UserId $userId,
        int $limit = 10
    ): AuthenticationHistoryDTO {
        $attempts = $this->loginAttemptRepository->findRecentByUserId($userId, $limit);

        $attemptDTOs = array_map(
            fn($attempt) => new AuthenticationAttemptDTO(
                attemptedAt: $attempt->getAttemptedAt(),
                success: $attempt->isSuccessful(),
                ipAddress: $attempt->getIpAddress(),
                userAgent: $attempt->getUserAgent(),
                failureReason: $attempt->getFailureReason(),
                method: $attempt->getMethod()
            ),
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
        $currentUser = $this->security->getUser();
        return $currentUser && $currentUser->getUserId()->equals($userId);
    }

    public function isDeviceTrusted(UserId $userId, string $deviceId): bool
    {
        // Pour le moment, pas de gestion de devices
        return false;
    }

    public function invalidateAllSessions(UserId $userId): void
    {
        // Avec les sessions Symfony, on ne peut invalider que la session courante
        $currentUser = $this->security->getUser();
        if ($currentUser && $currentUser->getUserId()->equals($userId)) {
            $this->requestStack->getSession()->invalidate();
        }

        // Note: Pour invalider TOUTES les sessions d'un user, il faudrait
        // un système custom ou utiliser Symfony's session handler avec DB
    }
}
