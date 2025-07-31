<?php

declare(strict_types=1);

namespace Authentication\Application\Service;

final readonly class LoginValidationResult
{
    private function __construct(
        public bool $isValid,
        public ?string $errorCode = null,
        public ?string $errorMessage = null,
    ) {}

    public static function success(): self
    {
        return new self(true);
    }

    public static function failed(string $errorCode, ?string $message = null): self
    {
        return new self(false, $errorCode, $message);
    }
}
