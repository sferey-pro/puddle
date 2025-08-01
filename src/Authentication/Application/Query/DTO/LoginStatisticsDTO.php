<?php

namespace Authentication\Application\Query\DTO;

final readonly class LoginStatisticsDTO
{
    public function __construct(
        public int $totalAttempts,
        public int $successfulLogins,
        public int $failedAttempts,
        public ?\DateTimeImmutable $lastSuccessfulLogin,
        public ?\DateTimeImmutable $lastFailedAttempt,
        public int $suspiciousActivities,
        public int $uniqueDevices,
        public ?int $averageTimeBetweenLogins // in seconds
    ) {}
}
