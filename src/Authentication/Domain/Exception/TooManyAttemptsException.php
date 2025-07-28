<?php

declare(strict_types=1);

namespace Authentication\Domain\Exception;

/**
 * Rate limiting exceptions
 */
final class TooManyAttemptsException extends AuthenticationException
{
    protected string $errorCode = 'AUTH_003';

    public static function forEmail(string $email, int $waitMinutes): self
    {
        $exception = new self(
            sprintf(
                'Too many attempts for %s. Please wait %d minutes before trying again.',
                $email,
                $waitMinutes
            )
        );
        $exception->context = [
            'identifier' => $email,
            'wait_minutes' => $waitMinutes,
            'retry_after' => (new \DateTime("+{$waitMinutes} minutes"))->format('c')
        ];
        return $exception;
    }

    public static function forPhone(string $phone, int $waitSeconds): self
    {
        $exception = new self(
            sprintf(
                'Too many attempts. Please wait %d seconds before requesting a new code.',
                $waitSeconds
            )
        );
        $exception->context = [
            'identifier' => $phone,
            'wait_seconds' => $waitSeconds,
            'retry_after' => (new \DateTime("+{$waitSeconds} seconds"))->format('c')
        ];
        return $exception;
    }
}
