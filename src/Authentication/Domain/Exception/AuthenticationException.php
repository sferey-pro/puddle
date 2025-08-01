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

    public static function invalidIdentifierType(string $message): self
    {
        return new class($message) extends AuthenticationException {
            protected string $errorCode = 'AUTH_008';
        };
    }

    public static function invalidIdentifierFormat(?string $reason = null): self
    {
        $message = 'Invalid identifier format.';
        if ($reason) {
            $message .= ' Reason: ' . $reason;
        }
        return new class($message) extends AuthenticationException {
            protected string $errorCode = 'AUTH_007';
        };

    }

    public static function registrationNotAllowed(?string $reason = null): self
    {
        $message = 'Registration is not allowed.';
        if ($reason) {
            $message .= ' Reason: ' . $reason;
        }
        return new class($message) extends AuthenticationException {
            protected string $errorCode = 'AUTH_006';
        };
    }

    public static function loginNotAllowed(string $errorCode, ?string $reason = null): self
    {
        $message = 'Login is not allowed.';
        if ($reason) {
            $message .= ' Reason: ' . $reason;
        }
        $exception = new class($message) extends AuthenticationException {
            protected string $errorCode = 'AUTH_009';
        };

        $exception->errorCode = $errorCode; // Override with specific error code from validation
        $exception->context = ['reason' => $reason];

        return $exception;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
