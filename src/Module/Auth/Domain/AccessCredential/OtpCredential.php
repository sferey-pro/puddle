<?php

namespace App\Module\Auth\Domain\AccessCredential;

final readonly class OtpCredential implements AccessCredential
{
    public function __construct(
        public string $code
    ) {}

    public function getType(): string
    {
        return 'otp';
    }
}