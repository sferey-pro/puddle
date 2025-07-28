<?php

declare(strict_types=1);

namespace Authentication\Domain\Repository;

use Authentication\Domain\Model\LoginAttempt;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface LoginAttemptRepositoryInterface
{
    // ========== CRUD ==========
    public function save(LoginAttempt $attempt): void;

    // ========== QUERY ==========
    public function findRecentByUserId(UserId $userId, int $limit = 10): array;
    public function countRecentAttempts(string $identifier, \DateInterval $period): int;
    public function countRecentAttemptsFromIp(string $ipAddress, \DateInterval $period): int;

    // ========== DELETE ==========
    public function removeOlderThan(\DateTimeInterface $before): int;
}
