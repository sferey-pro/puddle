<?php

declare(strict_types=1);

namespace SharedKernel\Domain\DTO\Authentication;

use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class ActiveSessionsDTO
{
    /**
     * @param SessionInfoDTO[] $sessions
     */
    public function __construct(
        public UserId $userId,
        public array $sessions,
        public int $totalCount
    ) {}

    public function getMostRecentSession(): ?SessionInfoDTO
    {
        if (empty($this->sessions)) {
            return null;
        }

        $sorted = $this->sessions;
        usort($sorted, fn($a, $b) => $b->lastActivityAt <=> $a->lastActivityAt);

        return $sorted[0];
    }

    public function getSessionsFromDevice(string $deviceId): array
    {
        return array_filter(
            $this->sessions,
            fn(SessionInfoDTO $session) => $session->deviceId === $deviceId
        );
    }
}
