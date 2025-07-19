<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Model;

final class RegistrationConfig
{
    public function isEnabled(): bool
    {
        return true;
    }

    public function hasRegistrationPeriod(): bool
    {
        return false;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }

    public function hasUserLimit(): bool
    {
        return false;
    }

    public function getUserLimit(): int
    {
        return 0;
    }

    public function hasGeoRestrictions(): bool
    {
        return false;
    }

    public function isCountryAllowed(string $country): bool
    {
        return true;
    }
}
