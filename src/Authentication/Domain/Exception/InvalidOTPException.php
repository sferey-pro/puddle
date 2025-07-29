<?php

declare(strict_types=1);

namespace Authentication\Domain\Exception;

/**
 * Invalid credential exceptions
 */
final class InvalidOTPException extends AuthenticationException
{
    protected string $errorCode = 'AUTH_002';

    public static function incorrect(int $attemptsLeft): self
    {
        $exception = new self(
            sprintf('Incorrect code. %d attempts remaining.', $attemptsLeft)
        );
        $exception->context = ['attempts_left' => $attemptsLeft];
        return $exception;
    }

    public static function expired(): self
    {
        return new self('This code has expired. Please request a new one.');
    }

    public static function tooManyAttempts(): self
    {
        return new self('Too many incorrect attempts. Please request a new code.');
    }

    public static function alreadyUsed(): self
    {
        return new self('This code has already been used.');
    }

    public static function notFound(): self
    {
        return new self('Invalid code.');
    }
}
