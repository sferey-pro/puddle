<?php

declare(strict_types=1);

namespace Authentication\Domain\Service;

interface OTPServiceInterface
{
    public function generateOTP(string $identifier): string;
    public function verifyOTP(string $identifier, string $code): bool;
    public function storeOTP(string $identifier, string $code, int $ttl = 900): void;
}
