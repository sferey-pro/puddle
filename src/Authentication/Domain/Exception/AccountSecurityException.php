<?php

declare(strict_types=1);

namespace Authentication\Domain\Exception;

/**
 * Security exceptions
 */
final class AccountSecurityException extends AuthenticationException
{
    protected string $errorCode = 'AUTH_004';

    public static function suspiciousActivity(string $reason): self
    {
        $exception = new self(
            'Suspicious activity detected. Please contact support if you believe this is an error.'
        );
        $exception->context = ['reason' => $reason];
        return $exception;
    }

    public static function accountLocked(): self
    {
        return new self('This account has been locked for security reasons. Please contact support.');
    }

    public static function ipBlocked(string $ip): self
    {
        $exception = new self('Access denied from this location.');
        $exception->context = ['ip' => $ip];
        return $exception;
    }
}
