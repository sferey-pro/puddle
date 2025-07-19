<?php

declare(strict_types=1);

namespace Authentication\Domain\Exception;

/**
 * Base exception for Authentication context
 */
abstract class AuthenticationException extends \DomainException
{
    protected string $errorCode;
    protected array $context = [];

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
