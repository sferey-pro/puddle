<?php
declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Authentication;

use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class AuthenticationHistoryDTO
{
    /**
     * @param AuthenticationAttemptDTO[] $attempts
     */
    public function __construct(
        public UserId $userId,
        public array $attempts,
        public int $totalCount
    ) {}

    public function getFailedAttempts(): array
    {
        return array_filter(
            $this->attempts,
            fn(AuthenticationAttemptDTO $attempt) => !$attempt->success
        );
    }

    public function getRecentFailureCount(int $minutes = 30): int
    {
        $threshold = new \DateTimeImmutable("-{$minutes} minutes");

        return count(array_filter(
            $this->getFailedAttempts(),
            fn(AuthenticationAttemptDTO $attempt) => $attempt->attemptedAt > $threshold
        ));
    }
}
